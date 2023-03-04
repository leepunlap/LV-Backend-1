<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $data = Hotel::all();

        return response()->json(array(
            'status' => true,
            'data' => $data,
            'message' => 'dataLoadedSuccessfully'
        ));
    }
}
