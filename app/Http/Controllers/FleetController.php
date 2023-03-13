<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FleetController extends Controller
{
    public function getFleets()
    {
        $data = Aircraft::with('images')->orderBy('model', 'ASC')->simplePaginate(5);
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Data loaded successfully'
        ]);
    }

    public function getFleet($id)
    {
        if (!$id)
            return response()->json([
                'status' => false,
                'message' => 'Requested Fleet not found!'
            ]);
        if (!$fleet = Aircraft::with('type', 'manufacture', 'images', 'amenities')->where(['id' => $id])->first())
            return response()->json([
                'status' => false,
                'message' => 'Requested Fleet details not found!'
            ]);

        return response()->json([
            'status' => true,
            'data' => $fleet,
            'message' => 'Fleet details retrieved successfully!'
        ]);
    }

    public function getPopularFleets()
    {
        $data = Booking::with('aircraft.images')->select(DB::raw('aircrafts_id, COUNT(aircrafts_id) as count'))->groupBy('aircrafts_id')->orderBy('count', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Data loaded successfully'
        ]);
    }
}
