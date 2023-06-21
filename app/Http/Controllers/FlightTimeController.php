<?php

namespace App\Http\Controllers;

use App\Models\FlightTime;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class FlightTimeController extends Controller
{
    public function get_flight_time(Request $request,  $aircraft_id = 0, $origin_id = 0, $destination_id = 0)
    {
        $returnFlightTimeInterval = 0;
        if ($aircraft_id && $origin_id && $destination_id) {
            $flightTime = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $origin_id, 'destination_id' => $destination_id])->first()->flight_time;

            if ($request->return == 'true') {
                $returnFlightTime = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $destination_id, 'destination_id' => $origin_id])->first()->flight_time;

                $returnFlightTimeDuration = DateTime::createFromFormat('H:i', $returnFlightTime);
                $returnFlightTimeInterval = new DateInterval('PT' . $returnFlightTimeDuration->format('H') . 'H' . $returnFlightTimeDuration->format('i') . 'M');

                $returnFlightTimeInterval = ($returnFlightTimeInterval->h * 60 + $returnFlightTimeInterval->i) / 60;
            }

            $flightTimeDuration = DateTime::createFromFormat('H:i', $flightTime);
            $flightTimeInterval = new DateInterval('PT' . $flightTimeDuration->format('H') . 'H' . $flightTimeDuration->format('i') . 'M');

            $totalHours = (($flightTimeInterval->h * 60 + $flightTimeInterval->i) / 60) + $returnFlightTimeInterval;





            if (isset($totalHours) && $totalHours != null && $totalHours != 0) {
                return response()->json([
                    'status' => true,
                    'flight_time' => $totalHours,
                    'message' => 'Data loaded successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'No Flight Time found in the records!'
                ]);
            }
        }
    }
}
