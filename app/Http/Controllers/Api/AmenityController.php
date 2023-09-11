<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftAmenity;
use App\Models\AircraftImage;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

use function GuzzleHttp\Promise\all;

class AmenityController extends Controller
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

    public function manage(Request $request, $id = null)
    {
        $input = $request->all();
        $input['requested_by'] = Auth::id();
        $input['status'] = 1;

        if ($request->id && Amenity::find($request->id)->id) {
            $amenity = Amenity::where(['id' => $request->id])->update($input);

            return response()->json([
                'status' => true,
                'message' => 'Amenity updated sccessfully!',
            ]);
        } else {
            $amenity = Amenity::create($input);

            if ($amenity->id) {

                return response()->json([
                    'status' => true,
                    'message' => 'Amenity requested sccessfully!',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!',
        ]);
    }

    public function delete($id)
    {
        if ($id && Amenity::find($id)->id) {
            Amenity::where(['id' => $id])->delete();

            return response()->json([
                'status' => true,
                'message' => 'Amenity deleted Successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Amenity not Found!'
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