<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\ChargeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargeController extends Controller
{
    public function index()
    {
        $data = Charge::with('charge_type', 'airport.country', 'aircraft')->where(['created_by' => Auth::id()])->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function ground_handling_charges(Request $request)
    {
        $additional_charge_id = ChargeType::where('name', 'LIKE', '%Ground%')->get();
        $ids = array_column($additional_charge_id->all(), 'id');
        if ($request->aircraft_id) {
            $data = Charge::with('charge_type', 'airport')->whereIn('charge_type_id', $ids)->where(['aircraft_id' => $request->aircraft_id, 'created_by' => Auth::id()])->orderBy('id', 'DESC')->get();
        } else {
            $data = Charge::with('charge_type', 'airport')->whereIn('charge_type_id', $ids)->where(['aircraft_id' => $request->aircraft_id, 'created_by' => Auth::id()])->orderBy('id', 'DESC')->get();
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function additional_charges(Request $request)
    {
        $additional_charge_id = ChargeType::where('name', 'LIKE', '%Additional Charges%')->get();
        $ids = array_column($additional_charge_id->all(), 'id');
        if ($request->aircraft_id) {
            $data = Charge::with('charge_type', 'airport')->whereIn('charge_type_id', $ids)->where(['aircraft_id' => $request->aircraft_id, 'created_by' => Auth::id()])->orderBy('id', 'DESC')->get();
        } else {
            $data = Charge::with('charge_type', 'airport')->whereIn('charge_type_id', $ids)->where(['aircraft_id' => $request->aircraft_id, 'created_by' => Auth::id()])->orderBy('id', 'DESC')->get();
        }
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function manage(Request $request, $id)
    {
        $input = $request->all();
        $input['updated_by'] = Auth::id();
        if ($id && Charge::find($id)->id) {

            $charge = Charge::where(['id' => $id])->update($input);

            return response()->json([
                'status' => true,
                'message' => 'Charge updated sccessfully!',
            ]);
        } else {
            $input['created_by'] = Auth::id();
            $charge = Charge::create($input);

            if ($charge->id) {
                return response()->json([
                    'status' => true,
                    'message' => 'Charge added sccessfully!',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Charge Type not added successfully!',
        ]);
    }

    public function show($id)
    {
        if ($id) {
            $data = Charge::where(['id' => $id])->first();

            return response()->json([
                'status' => true,
                'data' => $data,
                'api_url' => asset('storage/')
            ]);
        }
    }

    public function delete($id)
    {
        if ($id && Charge::find($id) && Charge::where(['id' => $id])->delete()) {

            return response()->json([
                'status' => true,
                'message' => 'Charge Type deleted Successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Charge Type not Found!'
        ]);
    }
}
