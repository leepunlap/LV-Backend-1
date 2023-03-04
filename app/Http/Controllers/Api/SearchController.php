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
use Illuminate\Http\Request;
use stdClass;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $airportModel = new Airport();
            $data['route'] = $request->type;
            $data['origins'] = $request->origin[0];
            $data['destinations'] = $request->destination[0];
            $data['departureDates'] = $request->departureDate[0];
            if ($data['route'] == 'one-way') {
                $data['origin'] = $airportModel->getAirportByName($data['origins']);
                $data['destination'] = $airportModel->getAirportByName($data['destinations']);
                $data['availableAircrafts'] =
                    AirportCharge::with(['originAirport', 'destinationAirport', 'equipment.amenities', 'equipment.images'])->where([
                        'origin_airport_id' => $data['origin']->id,
                        'destination_airport_id' => $data['destination']->id,
                        // 'date' => $data['departureDates']
                    ])->get();

                foreach ($data['availableAircrafts'] as $k => $v) {
                    $temp = $this->getAmount($v, $data['origin'], $data['destination']);
                    $data['availableAircrafts'][$k]->totalCharges = $temp['totalCharges'];
                    $data['availableAircrafts'][$k]->flightTime = $temp['flightTime'];
                }
                $data['departure'] = $data['departureDates'][0];
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

        // Flight Time
        $data['flightHours'] = ($data['enRouteCharges']->total_distance_flown_km / $data['aircraftDetails']->max_speed);

        $data['flightTime'] = gmdate('g\h\r i\m\i\n', $data['flightHours'] * 3600);

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
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Data Loaded Successfully!'
        ]);
    }
}
