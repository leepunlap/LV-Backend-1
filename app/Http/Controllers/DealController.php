<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        $data = Deal::all();

        return response()->json(array(
            'status' => true,
            'data' => $data,
            'message' => 'dataLoadedSuccessfully'
        ));
    }
}
