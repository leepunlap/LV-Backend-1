<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftAmenity;
use App\Models\AircraftImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

use function GuzzleHttp\Promise\all;

class AircraftController extends Controller
{
    public function index()
    {
        $data = Aircraft::with('amenities', 'prevImages', 'type')->where(['operator_id' => Auth::id()])->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        if ($id) {
            $data = Aircraft::with('prevImages')->where(['id' => $id])->first();
            $data->prevAmenities = AircraftAmenity::where(['aircraft_id' => $id])->pluck('amenities_id');

            return response()->json([
                'status' => true,
                'data' => $data,
                'api_url' => asset('storage/')
            ]);
        }
    }

    public function manage(Request $request, $id)
    {
        // header("Access-Control-Allow-Origin: *");
        // dd($request->all());
        $input = $request->all();
        $input['aircraft_length'] = json_encode(['ft' => $input['aircraft_length']['ft'], 'in' => $input['aircraft_length']['in'], 'm' => $input['aircraft_length']['m']]);
        $input['cabin_length'] = json_encode(['ft' => $input['cabin_length']['ft'], 'in' => $input['cabin_length']['in'], 'm' => $input['cabin_length']['m']]);
        $input['cabin_height'] = json_encode(['ft' => $input['cabin_height']['ft'], 'in' => $input['cabin_height']['in'], 'm' => $input['cabin_height']['m']]);
        $input['aircraft_width'] = json_encode(['ft' => $input['aircraft_width']['ft'], 'in' => $input['aircraft_width']['in'], 'm' => $input['aircraft_width']['m']]);
        $input['cabin_width'] = json_encode(['ft' => $input['cabin_width']['ft'], 'in' => $input['cabin_width']['in'], 'm' => $input['cabin_width']['m']]);
        $input['owner_approval'] = (int) $input['owner_approval'] ;
        $input['operator_id'] = Auth::id();

        if ($id && Aircraft::find($id)->id) {
            $availableColumns = [
                'operator_id',
                'model',
                'manufacture',
                'type',
                'mtow',
                'lv_margin',
                'aircraft_length',
                'cabin_length',
                'cabin_height',
                'aircraft_width',
                'cabin_width',
                'max_speed',
                'max_range',
                'max_altitude',
                'fuel_burn_per_hour',
                'capacity',
                'pax',
                'no_of_crew',
                'hourly_rate',
                'crew_per_diem',
                'crew_hotel',
                'total_crew_cost',
                'margin',
                'wifi',
                'full_size_bathrooms',
                'meal_service',
                'pet_accomodation',
                'wide_screen_televisions',
                'ambient_lighting',
                'cabin_crew',
                'registration_no',
                'manufacture_year',
                'refurbishment_year',
                'owner_approval',
                'mtow_unit',
                'alignment_unit'
            ];
            $dataToUpdate = Arr::only($input, $availableColumns);
            $aircraft = Aircraft::where(['id' => $id])->update($dataToUpdate);

            if (isset($request->prev_images) && count($request->prev_images) > 0) {
                $ids = AircraftImage::where(['aircraft_id' => $id])->pluck('id');
                $prevIds = array_column($request->prev_images, 'id');

                foreach ($ids as $key => $value) {
                    if (!in_array($value, $prevIds)) {
                        AircraftImage::where(['id' => $value])->delete();
                    }
                }
            } else {
                AircraftImage::where(['aircraft_id' => $id])->delete();
            }

            AircraftAmenity::where(['aircraft_id' => $id])->delete();

            if ($request->amenities && count($request->amenities) > 0) {
                foreach ($request->amenities as $key => $value) {
                    if ($value === 'true') {
                        AircraftAmenity::create([
                            'aircraft_id' => $id,
                            'amenities_id' => str_replace("id_", "", $key)
                        ]);
                    }
                }
            }

            if ($request->file('images') && count($request->file('images')) > 0) {
                foreach ($request->file('images') as $file) {
                    $file_name = $file->getClientOriginalName();
                    $path = $file->storeAs('public',  $file->getClientOriginalName());
                    AircraftImage::create([
                        'aircraft_id' => $id,
                        'name' => $file_name,
                        'uploaded_file' => $path,
                        'status' => 1
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Aircraft updated sccessfully!',
            ]);
        } else {
            $aircraft = Aircraft::create($input);

            if ($aircraft->id) {

                if ($request->amenities && count($request->amenities) > 0) {
                    foreach ($request->amenities as $key => $value) {
                        if ($value === 'true') {
                            AircraftAmenity::create([
                                'aircraft_id' => $aircraft->id,
                                'amenities_id' => str_replace("id_", "", $key)
                            ]);
                        }
                    }
                }
                if ($request->file('images') && count($request->file('images')) > 0) {
                    foreach ($request->file('images') as $file) {
                        $file_name = $file->getClientOriginalName();
                        $path = $file->storeAs('public',  $file->getClientOriginalName());
                        AircraftImage::create([
                            'aircraft_id' => $aircraft->id,
                            'name' => $file_name,
                            'uploaded_file' => $path,
                            'status' => 1
                        ]);
                    }
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Aircraft added sccessfully!',
                ]);
            }
        }


        return response()->json([
            'status' => false,
            'message' => 'Aircraft not added successfully!',
        ]);
    }

    public function delete($id)
    {
        if ($id && Aircraft::find($id)->id) {
            AircraftAmenity::where(['aircraft_id' => $id])->delete();
            AircraftImage::where(['aircraft_id' => $id])->delete();
            Aircraft::where(['id' => $id])->delete();

            return response()->json([
                'status' => true,
                'message' => 'Fleet deleted Successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Fleet not Found!'
        ]);
    }

    public function duplicate($id)
    {
        if (!$id || !$aircraft = Aircraft::with('images', 'amenities')->where(['id' => $id])->first()) {
            return response()->json([
                'status' => false,
                'message' => 'Aircraft not found! <br />Please refresh page',
            ]);
        }

        $new_aircraft = Aircraft::create($aircraft->toArray());
        $images = array_filter($aircraft['images']->all(), function ($e) use ($new_aircraft) {
            $e['aircraft_id'] = $new_aircraft->id;
            AircraftImage::create($e->toArray());
        });
        array_filter($aircraft['amenities']->all(), function ($e) use ($new_aircraft) {
            $e['aircraft_id'] = $new_aircraft->id;
            AircraftAmenity::create($e->toArray());
        });

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Aircraft of <strong>' . $aircraft['model'] . '</strong> created!<br /> Redirecting...',
            'data' => $new_aircraft ?? ''
        ]);
    }
}
