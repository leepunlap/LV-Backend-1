<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\AircraftManufacture;
use App\Models\AircraftType;
use App\Models\Airport;
use App\Models\Amenity;
use App\Models\ChargeType;
use App\Models\City;
use App\Models\Country;
use App\Models\AppSetting;
use App\Models\UserSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function getCountries(Request $request)
    {
        if ($request->id) {
            $data = Country::find()->where(['id' => $request->id])->first();
        } else {
            $data = Country::all();
        }

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Data Loaded!',
                'data' => $data,
                'error' => ''
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data Loaded!',
                'data' => [],
                'error' => 'Something Went Wrong'
            ]);
        }
    }

    public function getCities(Request $request)
    {
        if ($request->id) {
            $data = City::find()->where(['id' => $request->id])->first();
        } else {
            $data = City::all();
        }

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Data Loaded!',
                'data' => $data,
                'error' => ''
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data Loaded!',
                'data' => [],
                'error' => 'Something Went Wrong'
            ]);
        }
    }

    public function getAirports(Request $request)
    {
        try {
            if ($request->id) {
                return response()->json([
                    'status' => true,
                    'data' => Airport::select('id', 'name', 'icao', 'iata', 'latitude', 'longitude')->with('city', 'country')->where(['id' => $request->id])->first(),
                ]);
            } else if ($request->q) {
                $q = trim(explode(" (", $request->q)[0]);
                return response()->json([
                    'status' => true,
                    'data' => Airport::with(['city', 'country'])
                        ->where(function ($query) use ($q) {
                            $query->where('name', 'LIKE', "%{$q}%")
                                ->orWhere('icao', 'LIKE', "%{$q}%")
                                ->orWhere('iata', 'LIKE', "%{$q}%")
                                ->orWhereHas('city', function ($query) use ($q) {
                                    $query->where('name', 'LIKE', "%{$q}%");
                                })
                                ->orWhereHas('country', function ($query) use ($q) {
                                    $query->where('name', 'LIKE', "%{$q}%");
                                });
                        })
                        ->limit(10)
                        ->get(),
                ], 200);
            } else {
                $data = Airport::select('id', 'name', 'icao', 'iata', 'latitude', 'longitude');

                if ($search = $request->get('search')) {
                    $data->whereRaw("name LIKE '%" . $search . "%' OR icao LIKE '%" . $search . "%' OR iata LIKE '%" . $search . "%'");
                }

                $data->orderBy('icao', 'ASC');

                if ($page = $request->get('page')) {
                    $data->offset((($page - 1) * 10))->limit(10);
                }

                return response()->json([
                    'status' => true,
                    'data' => $data->get(),
                ], 200);

            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getManufactures(Request $request)
    {
        try {
            if ($request->id) {
                return response()->json([
                    'status' => true,
                    'data' => AircraftManufacture::select('id', 'name')->where(['id' => $request->id])->first(),
                ]);
            } else if ($request->q) {
                return response()->json([
                    'status' => true,
                    'data' => AircraftManufacture::whereRaw("name LIKE '%" . $request->q . "%'")->limit(10)->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => AircraftManufacture::select('id', 'name')->limit(10)->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getTypes(Request $request)
    {
        try {
            if ($request->id) {
                return response()->json([
                    'status' => true,
                    'data' => AircraftType::select('id', 'name')->where(['id' => $request->id])->first(),
                ]);
            } else if ($request->q) {
                return response()->json([
                    'status' => true,
                    'data' => AircraftType::whereRaw("name LIKE '%" . $request->q . "%'")->limit(10)->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => AircraftType::select('id', 'name')->limit(10)->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getAmenities(Request $request, $id = null)
    {
        try {
            if ($id) {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::select('id', 'name', 'description')->where(['id' => $id])->first(),
                ]);
            } else if ($request->id) {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::select('id', 'name', 'description')->where(['status' => 1, 'id' => $request->id])->first(),
                ]);
            } else if ($request->q) {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::whereRaw("status = 1 AND name LIKE '%" . $request->q . "%'")->limit(10)->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::select('id', 'name', 'description')->where(['status' => 1])->limit(10)->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function setAmenities(Request $request)
    {
        dd($request->post());
        try {
            if ($request->id) {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::select('id', 'name')->where(['status' => 1, 'id' => $request->id])->first(),
                ]);
            } else if ($request->q) {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::whereRaw("status = 1 AND name LIKE '%" . $request->q . "%'")->limit(10)->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => Amenity::select('id', 'name')->where(['status' => 1])->limit(10)->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getSearch($id = 0, $user_id = 0)
    {
        try {
            if ($id) {
                return response()->json([
                    'status' => true,
                    'data' => UserSearch::select('id', 'params', 'name')->where(['id' => $id])->get(),
                ]);
            } else if ($user_id) {
                return response()->json([
                    'status' => true,
                    'data' => UserSearch::select('id', 'params', 'name')->where(['user_id' => $user_id])->get(),
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => UserSearch::select('id', 'params', 'name')->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getChargeTypes($id = 0, $user_id = 0)
    {
        try {
            if ($id) {
                return response()->json([
                    'status' => true,
                    'data' => ChargeType::with('country')->where(['id' => $id])->get(),
                ]);
            } else if ($user_id) {
                return response()->json([
                    'status' => true,
                    'data' => ChargeType::with('country')->where(['created_by' => $user_id])->get(),
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => ChargeType::with('country')->get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getAircrafts($id = 0, $user_id = 0)
    {
        try {
            if ($id) {
                return response()->json([
                    'status' => true,
                    'data' => Aircraft::where(['id' => $id])->get(),
                ]);
            } else if ($user_id) {
                return response()->json([
                    'status' => true,
                    'data' => Aircraft::where(['created_by' => $user_id])->get(),
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => Aircraft::get(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getAircraftsByOperator()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => Aircraft::where(['operator_id' => Auth::id()])->get(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'error' => $th,
                'mesage' => 'Internal Server Error!'
            ]);
        }
    }

    public function getAppSetting($key)
    {
        $setting = AppSetting::where('key', $key)->first();
        if (!$setting) {
            return response()->json(['status' => false, 'message' => 'Setting not found'], 404);
        }
        return response()->json(['status' => true, 'key' => $setting->key, 'value' => $setting->value]);
    }
}