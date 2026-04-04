<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftDetail;
use App\Models\Airport;
use App\Models\AirportBillingSource;
use App\Models\AirportCharge;
use App\Models\AirportChargeCategory;
use App\Models\AirportChargeElement;
use App\Models\AirportParameter;
use App\Models\Enroute;
use App\Models\EnrouteChargeElement;
use App\Models\EnrouteChargeItem;
use App\Models\EnrouteCountry;
use App\Models\UserSearch;
use App\Services\FlightDistanceService;
use Illuminate\Http\Request;
use stdClass;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $airportModel = new Airport();
            $data['route'] = $request->type ?? 'one-way';
            
            // Handle both array and string formats
            $origins = is_array($request->origin) ? $request->origin : [$request->origin];
            $destinations = is_array($request->destination) ? $request->destination : [$request->destination];
            $departureDates = is_array($request->departureDate) ? $request->departureDate : [$request->departureDate];
            
            $data['origins'] = $origins[0] ?? null;
            $data['destinations'] = $destinations[0] ?? null;
            $data['departureDates'] = $departureDates[0] ?? null;
            
            if (!$data['origins'] || !$data['destinations'] || !$data['departureDates']) {
                return response()->json(['status' => false, 'message' => 'Missing required parameters'], 400);
            }
            
            if ($data['route'] == 'one-way') {
                // Parse airport codes from format "City (CODE)"
                preg_match('/\(([^)]+)\)/', $data['origins'], $originMatch);
                preg_match('/\(([^)]+)\)/', $data['destinations'], $destMatch);
                
                $originCode = $originMatch[1] ?? null;
                $destCode = $destMatch[1] ?? null;
                
                $data['origin'] = Airport::where('iata', $originCode)->first();
                $data['destination'] = Airport::where('iata', $destCode)->first();
                
                if (!$data['origin'] || !$data['destination']) {
                    return response()->json(['status' => false, 'message' => 'Airport not found'], 400);
                }
                
                // Search for enroutes matching this route and date
                $enroutes = Enroute::where([
                    'origin_airport_id' => $data['origin']->id,
                    'destination_airport_id' => $data['destination']->id,
                    'date' => $data['departureDates']
                ])->get();
                
                // Get all active aircraft and attach pricing information
                $aircraft_collection = Aircraft::where('status', 'Active')
                    ->with(['charges', 'amenities', 'images', 'type'])
                    ->get();
                
                if ($enroutes->isEmpty()) {
                    $distanceService = app(FlightDistanceService::class);
                    $result = [];
                    foreach ($aircraft_collection as $aircraft) {
                        $responseAircraft = new stdClass();
                        $responseAircraft->id = $aircraft->id;
                        $responseAircraft->name = $aircraft->name;
                        $responseAircraft->model = $aircraft->model ?? $aircraft->name;
                        $responseAircraft->pax = $aircraft->pax;
                        $responseAircraft->max_speed = $aircraft->max_speed;
                        $responseAircraft->fuel_burn_per_hour = $aircraft->fuel_burn_per_hour;
                        $responseAircraft->hourly_rate = $aircraft->hourly_rate;
                        $responseAircraft->total_crew_cost = $aircraft->total_crew_cost;
                        $responseAircraft->equipment_id = $aircraft->equipment_id;
                        $responseAircraft->status = $aircraft->status;
                        $responseAircraft->owner_approval = $aircraft->owner_approval ?? 0;
                        $responseAircraft->amenities = $aircraft->amenities ?? [];
                        $responseAircraft->images = $aircraft->images ?? [];
                        $responseAircraft->charges = $aircraft->charges ?? [];
                        $responseAircraft->type = $aircraft->type;
                        $responseAircraft->origin_airport = $data['origin'];
                        $responseAircraft->destination_airport = $data['destination'];

                        $distanceKm = $distanceService->getDistance($data['origin'], $data['destination']);
                        $cruiseSpeed = (float) $aircraft->max_speed ?: 800;
                        $flightHours = $distanceService->flightHours($distanceKm, $cruiseSpeed);

                        $rangeNm = (float) str_replace(',', '', $aircraft->max_range ?? 0);
                        $routeNm = $distanceKm / 1.852;

                        $responseAircraft->distance_km = $distanceKm;
                        $responseAircraft->flightTime = $distanceService->estimateFlightTime($distanceKm, $cruiseSpeed);
                        $responseAircraft->currency = 'USD';
                        $responseAircraft->totalCharges = round((float)$aircraft->hourly_rate * $flightHours + (float)$aircraft->total_crew_cost, 2);
                        $responseAircraft->range_nm = round($rangeNm, 0);
                        $responseAircraft->route_nm = round($routeNm, 0);
                        $responseAircraft->range_status = $rangeNm <= 0 ? 'unknown'
                            : ($rangeNm >= $routeNm * 1.1 ? 'nonstop'
                            : ($rangeNm >= $routeNm ? 'possible' : 'fuel_stop'));
                        $responseAircraft->equipment = clone $responseAircraft;
                        unset($responseAircraft->equipment->equipment);
                        $result[] = $responseAircraft;
                    }
                    $data['availableAircrafts'] = $result;
                    $data['departure'] = $data['departureDates'];
                    $data['trip_type'] = $data['route'];
                    $data['status'] = true;
                    return response()->json($data);
                }
                
                // Add pricing from the first matching enroute
                $enroute = $enroutes->first();
                $result = [];
                
                foreach ($aircraft_collection as $aircraft) {
                    // Create a clean response object to avoid circular references
                    $responseAircraft = new stdClass();
                    
                    // Copy basic aircraft properties
                    $responseAircraft->id = $aircraft->id;
                    $responseAircraft->name = $aircraft->name;
                    $responseAircraft->model = $aircraft->model ?? $aircraft->name;
                    $responseAircraft->pax = $aircraft->pax;
                    $responseAircraft->max_speed = $aircraft->max_speed;
                    $responseAircraft->fuel_burn_per_hour = $aircraft->fuel_burn_per_hour;
                    $responseAircraft->hourly_rate = $aircraft->hourly_rate;
                    $responseAircraft->total_crew_cost = $aircraft->total_crew_cost;
                    $responseAircraft->equipment_id = $aircraft->equipment_id;
                    $responseAircraft->status = $aircraft->status;
                    $responseAircraft->owner_approval = $aircraft->owner_approval ?? 0;
                    
                    // Add relations directly (no equipment wrapper to avoid circular refs)
                    $responseAircraft->amenities = $aircraft->amenities ?? [];
                    $responseAircraft->images = $aircraft->images ?? [];
                    $responseAircraft->charges = $aircraft->charges ?? [];
                    $responseAircraft->type = $aircraft->type;
                    
                    // Add airport and pricing information
                    $responseAircraft->origin_airport = $data['origin'];
                    $responseAircraft->destination_airport = $data['destination'];
                    $responseAircraft->totalCharges = (float)$enroute->subtotal;
                    $responseAircraft->currency = $enroute->subtotal_currency;
                    $responseAircraft->distance_km = $enroute->total_distance_flown_km;

                    $distanceService = app(FlightDistanceService::class);
                    $distanceKm = (float) $enroute->total_distance_flown_km
                        ?: $distanceService->getDistance($data['origin'], $data['destination']);
                    $cruiseSpeed = (float) $aircraft->max_speed ?: 800;
                    $responseAircraft->distance_km = $distanceKm;
                    $responseAircraft->flightTime = $distanceService->estimateFlightTime($distanceKm, $cruiseSpeed);

                    $rangeNm = (float) str_replace(',', '', $aircraft->max_range ?? 0);
                    $routeNm = $distanceKm / 1.852;
                    $responseAircraft->range_nm = round($rangeNm, 0);
                    $responseAircraft->route_nm = round($routeNm, 0);
                    $responseAircraft->range_status = $rangeNm <= 0 ? 'unknown'
                        : ($rangeNm >= $routeNm * 1.1 ? 'nonstop'
                        : ($rangeNm >= $routeNm ? 'possible' : 'fuel_stop'));

                    // Create equipment wrapper that mirrors this object
                    $responseAircraft->equipment = clone $responseAircraft;
                    unset($responseAircraft->equipment->equipment); // Remove circular reference
                    
                    $result[] = $responseAircraft;
                }
                
                $data['availableAircrafts'] = $result;
                
                $data['departure'] = $data['departureDates'];
                $data['trip_type'] = $data['route'];
            }
            $data['status'] = true;
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(['th' => $th->getMessage()]);
        }
    }

    public function getAmount($aircraft, $origin, $destination)
    {
        $airportChargesModel = new AirportCharge();
        $enRouteModel = new Enroute();
        $distanceService = app(FlightDistanceService::class);

        $data['origin'] = $origin;
        $data['destination'] = $destination;
        $data['aircraftDetails'] = Aircraft::with('charges')->where(['id' => $aircraft->equipment_id])->first();
        $data['airportCharges'] = $airportChargesModel->getDetailsByOriginDestinationAircraft($origin->id, $destination->id, $aircraft->equipment_id);
        $data['enRouteCharges'] = $enRouteModel->getDetailsByOriginDestination($origin->id, $destination->id);

        // For EnRoute Countries Charges
        $data['enRouteCharges']->countries = $this->getEnRouteCharges($data['enRouteCharges']);

        //For Airport Charges
        $data['airportCharges']->origin = $this->getAirportCharges($data['airportCharges'], 'origin');
        $data['airportCharges']->destination = $this->getAirportCharges($data['airportCharges'], 'destination');

        // Flight distance & time from great-circle calculation
        $distanceKm = (float) $data['enRouteCharges']->total_distance_flown_km
            ?: $distanceService->getDistance($origin, $destination);
        $cruiseSpeed = (float) ($data['aircraftDetails']->max_speed ?? 0) ?: 800;

        $data['distance_km']  = $distanceKm;
        $data['flightHours']  = $distanceService->flightHours($distanceKm, $cruiseSpeed);
        $data['flightTime']   = $distanceService->estimateFlightTime($distanceKm, $cruiseSpeed);

        // if ($trip == 'ONEWAY') {
        $data['origin_fuel_costs'] = $data['flightHours'] * $data['aircraftDetails']->fuel_burn_per_hour * $origin->fuel_price;
        $data['destination_fuel_costs'] = $data['flightHours'] * $data['aircraftDetails']->fuel_burn_per_hour * $destination->fuel_price;
        $data['total_fuel_costs'] = ($data['origin_fuel_costs']  + $data['destination_fuel_costs']);
        // }

        // Origin Airport Handling Costs
        $data['origin']->handling_costs = AirportParameter::where(['airports_id' => $data['origin']->id])->get();
        $data['origin_handling_costs'] = array_sum(array_column($data['origin']->handling_costs->all(), 'value'));

        // Destination Airport Handling Costs
        $data['destination']->handling_costs = AirportParameter::where(['airports_id' => $data['destination']->id])->get();
        $data['destination_handling_costs'] = array_sum(array_column($data['destination']->handling_costs->all(), 'value'));

        // Aircraft Charges
        $a_t_charges = 0;
        $a_f_charges = 0;

        if ($data['aircraftDetails']->charges) {

            if (count($data['aircraftDetails']->charges) > 0) {
                $a_t_charges = 0;
                $a_f_charges = 0;
                foreach ($data['aircraftDetails']->charges as $key => $value) {
                    if (!$value->after_flight) {
                        $a_t_charges += $value->value;
                    } else {
                        $a_f_charges += $value->value;
                    }
                }
            } else {
                $data['aircraftDetails']->charges = [];
            }
        } else {
            $data['aircraftDetails']->charges = [];
        }

        $data['aircraftDetails']->after_flight_charges = $a_f_charges;
        $data['aircraftDetails']->total_charges = $data['aircraftDetails']->total_crew_cost + $a_t_charges;
        $data['total_hours_costs'] = $data['aircraftDetails']->hourly_rate * $data['flightHours'];
        $data['totalCharges'] = $data['total_hours_costs'] + $data['total_fuel_costs'] + $data['origin_handling_costs'] + $data['destination_handling_costs'] + $data['airportCharges']->subtotal + $data['enRouteCharges']->subtotal + $data['aircraftDetails']->total_charges;
        return $data;
    }

    public function getEnRouteCharges($data)
    {
        $countries = array();
        $countries = EnrouteCountry::where(['enroutes_id' => $data->id])->get();
        foreach ($countries as $key => $value) {
            $countries[$key]->chargeElements = EnrouteChargeElement::where(['enroute_countries_id' => $value->id])->get();
            foreach ($countries[$key]->chargeElements as $k => $val) {
                $countries[$key]->chargeElements[$k]->chargeItems = EnrouteChargeItem::where(['enroute_charge_elements_id' => $val->id])->get();
            }
        }
        return $countries;
    }

    public function getAirportCharges($data, $type)
    {
        $billingSources = array();
        $billingSources = AirportBillingSource::where(['airport_charges_id' => $data->id, 'type' => $type])->get();
        foreach ($billingSources as $key => $value) {
            $billingSources[$key]->chargeCategories = AirportChargeCategory::where(['airport_billing_sources_id' => $value->id])->get();
            foreach ($billingSources[$key]->chargeCategories as $k => $val) {
                $billingSources[$key]->chargeCategories[$k]->chargeElements = AirportChargeElement::where(['airport_charge_categories_id' => $val->id])->get();
            }
        }
        return $billingSources;
    }

    public function addSearchHistory(Request $request)
    {
        try {
            $search = UserSearch::create([
                'search_id' => sha1(time()),
                'user_id' => $request->id ?? 0,
                'params' => json_encode($request->all()),
                'ip' => $request->server('REMOTE_ADDR'),
                'agent' => $request->server('HTTP_USER_AGENT'),
            ]);
        } catch (\Throwable $th) {
        }
    }

    public function getFlightDetails(Request $request, $id)
    {
        $data = AirportCharge::with(['originAirport', 'destinationAirport', 'equipment.amenities', 'equipment.images'])->where([
            'id' => $id,
        ])->first();
        $data->origin = Airport::where(['id' => $data->origin_airport_id])->first();
        $data->destination = Airport::where(['id' => $data->destination_airport_id])->first();
        $data->costCalculation = $this->getAmount($data, $data->origin, $data->destination);
        $data->totalCharges = $data->costCalculation['totalCharges'];
        $data->flightTime = $data->costCalculation['flightTime'];
        $data->distance_km = $data->costCalculation['distance_km'];
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Data Loaded Successfully!'
        ]);
    }

    public function getFlightDistance(Request $request, $origin, $destination)
    {
        $originAirport = Airport::where('iata', strtoupper($origin))->first();
        $destinationAirport = Airport::where('iata', strtoupper($destination))->first();

        if (!$originAirport || !$destinationAirport) {
            return response()->json(['status' => false, 'message' => 'Airport not found'], 404);
        }

        $service = app(FlightDistanceService::class);
        $distanceKm = $service->getDistance($originAirport, $destinationAirport);
        $bearingDeg = $service->calculateBearing($originAirport, $destinationAirport);

        return response()->json([
            'status'      => true,
            'origin'      => ['iata' => $originAirport->iata, 'name' => $originAirport->name],
            'destination' => ['iata' => $destinationAirport->iata, 'name' => $destinationAirport->name],
            'distance_km' => $distanceKm,
            'bearing_deg' => $bearingDeg,
        ]);
    }
}
