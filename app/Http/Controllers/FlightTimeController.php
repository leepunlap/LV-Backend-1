<?php

namespace App\Http\Controllers;

use App\Models\FlightTime;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class FlightTimeController extends Controller
{
    public function get_flight_time(Request $request, $aircraft_id = 0, $origin_id = 0, $destination_id = 0, $leg1_id = 0)
    {
        $returnFlightTimeInterval = 0;
        if ($aircraft_id && $origin_id && $destination_id) {

            if ($leg1_id) {
                // return response()->json([
                //     '$aircraft_id' => $aircraft_id,
                //     '$origin_id' => $origin_id,
                //     '$leg1_id' => $leg1_id,
                //     '$destination_id' => $destination_id,
                // ]);
                $flight1Time = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $origin_id, 'destination_id' => $leg1_id])->first()->flight_time;
                $flight2Time = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $leg1_id, 'destination_id' => $destination_id])->first()->flight_time;

                if ($request->return == 'true') {
                    $returnFlight1Time = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $destination_id, 'destination_id' => $leg1_id])->first()->flight_time;
                    $returnFlight2Time = FlightTime::where(['aircraft_id' => $aircraft_id, 'origin_id' => $leg1_id, 'destination_id' => $origin_id])->first()->flight_time;

                    $returnFlight1TimeDuration = DateTime::createFromFormat('H:i', $returnFlight1Time);
                    $returnFlight2TimeDuration = DateTime::createFromFormat('H:i', $returnFlight2Time);
                    $returnFlight1TimeInterval = new DateInterval('PT' . $returnFlight1TimeDuration->format('H') . 'H' . $returnFlight1TimeDuration->format('i') . 'M');
                    $returnFlight2TimeInterval = new DateInterval('PT' . $returnFlight2TimeDuration->format('H') . 'H' . $returnFlight2TimeDuration->format('i') . 'M');

                    $returnFlightTimeInterval = (($returnFlight1TimeInterval->h * 60 + $returnFlight1TimeInterval->i) / 60) + ($returnFlight2TimeInterval->h * 60 + $returnFlight2TimeInterval->i) / 60;
                }

                $flight1TimeDuration = DateTime::createFromFormat('H:i', $flight1Time);
                $flight1TimeInterval = new DateInterval('PT' . $flight1TimeDuration->format('H') . 'H' . $flight1TimeDuration->format('i') . 'M');
                $flight2TimeDuration = DateTime::createFromFormat('H:i', $flight2Time);
                $flight2TimeInterval = new DateInterval('PT' . $flight2TimeDuration->format('H') . 'H' . $flight2TimeDuration->format('i') . 'M');

                $totalHours = (($flight1TimeInterval->h * 60 + $flight1TimeInterval->i) / 60) + (($flight2TimeInterval->h * 60 + $flight2TimeInterval->i) / 60) + $returnFlightTimeInterval;

            } else {
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
            }

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