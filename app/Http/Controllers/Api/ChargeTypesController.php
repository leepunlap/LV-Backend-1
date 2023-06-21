<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChargeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargeTypesController extends Controller
{
    public function index()
    {
        $data = ChargeType::with('country')->where(['created_by' => Auth::id()])->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function manage(Request $request, $id)
    {
        $input = $request->all();
        $input['updated_by'] = Auth::id();
        if ($id && ChargeType::find($id)->id) {

            $charge_type = ChargeType::where(['id' => $id])->update($input);

            return response()->json([
                'status' => true,
                'message' => 'Charge Type updated sccessfully!',
            ]);
        } else {
            $input['created_by'] = Auth::id();
            $charge_type = ChargeType::create($input);

            if ($charge_type->id) {

                return response()->json([
                    'status' => true,
                    'message' => 'Charge Type added sccessfully!',
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
            $data = ChargeType::where(['id' => $id])->first();

            return response()->json([
                'status' => true,
                'data' => $data,
                'api_url' => asset('storage/')
            ]);
        }
    }

    public function delete($id)
    {
        if ($id && ChargeType::find($id) && ChargeType::where(['id' => $id])->delete()) {

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
