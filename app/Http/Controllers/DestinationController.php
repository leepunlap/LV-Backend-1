<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        $data = Destination::all();

        return response()->json(array(
            'status' => true,
            'data' => $data,
            'message' => 'dataLoadedSuccessfully'
        ));
    }
}
