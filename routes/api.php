<?php

use App\Http\Controllers\Api\AircraftController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\Api\ChargeTypesController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FlightTimeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Models\Airport;
use App\Models\Charge;
use App\Models\FlightTime;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::get('get-hotels', [HotelController::class, 'index']);
Route::get('get-deals', [DealController::class, 'index']);
Route::get('get-destinations', [DestinationController::class, 'index']);

Route::get('get-countries', [CommonController::class, 'getCountries']);
Route::get('get-cities', [CommonController::class, 'getCities']);
Route::get('get-airports', [CommonController::class, 'getAirports']);

Route::get('get-manufactures', [CommonController::class, 'getManufactures']);
Route::get('get-types', [CommonController::class, 'getTypes']);
Route::get('get-amenities', [CommonController::class, 'getAmenities']);
Route::get('get-types', [CommonController::class, 'getTypes']);

Route::get('search', [SearchController::class, 'index']);
Route::get('add-search-history', [SearchController::class, 'addSearchHistory']);
Route::get('get-flight-details/{id}', [SearchController::class, 'getFlightDetails']);
Route::get('get-client-secret', [PaymentController::class, 'index']);
Route::post('book-flight', [BookingController::class, 'store']);

Route::get('get-fleets', [FleetController::class, 'getFleets']);
Route::get('get-popular-fleets', [FleetController::class, 'getPopularFleets']);
Route::get('get-fleet/{id}', [FleetController::class, 'getFleet']);
Route::get('get-aircrafts', [CommonController::class, 'getAircrafts']);
Route::get('get-chargetypes', [CommonController::class, 'getChargeTypes']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('update-profile-picture', [UserController::class, 'updateProfileImage']);
    Route::get('user/profile', function (Request $request) {
        return $request->user();
    });

    Route::get('get-bookings', [BookingController::class, 'getAllBookings']);
    Route::get('get-booking-schedules', [BookingController::class, 'getBookingSchedules']);

    // Fleet
    Route::get('fleet', [AircraftController::class, 'index']);
    Route::get('fleet/{id}', [AircraftController::class, 'show']);
    Route::post('fleet/{id}', [AircraftController::class, 'manage']);
    Route::get('fleet/duplicate/{id}', [AircraftController::class, 'duplicate']);
    Route::delete('fleet/{id}', [AircraftController::class, 'delete']);
    Route::get('get-operator-aircrafts', [CommonController::class, 'getAircraftsByOperator']);


    // Charge Type
    Route::get('charge_types', [ChargeTypesController::class, 'index']);
    Route::get('charge_types/{id}', [ChargeTypesController::class, 'show']);
    Route::post('charge_types/{id}', [ChargeTypesController::class, 'manage']);
    Route::delete('charge_types/{id}', [ChargeTypesController::class, 'delete']);

    // Charge
    Route::get('charges', [ChargeController::class, 'index']);
    Route::get('ground-handling-charges', [ChargeController::class, 'ground_handling_charges']);
    Route::get('additional-charges', [ChargeController::class, 'additional_charges']);
    Route::get('charges/{id}', [ChargeController::class, 'show']);
    Route::post('charges/{id}', [ChargeController::class, 'manage']);
    Route::get('charges/duplicate/{id}', [ChargeController::class, 'duplicate']);
    Route::delete('charges/{id}', [ChargeController::class, 'delete']);
    Route::get('get-flight-time/{aircraft_id}/{origin_id}/{destination_id}', [FlightTimeController::class, 'get_flight_time']);
});


// Route::get('insert_data', function () {
//     $data = [
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RCTP',
//             'flight_time' => "1:25"
//         ],
//         [
//             'origin' => 'RCTP',
//             'destination' => 'VHHH',
//             'flight_time' => "1:30"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RCSS',
//             'flight_time' => "1:30"
//         ],
//         [
//             'origin' => 'RCSS',
//             'destination' => 'VHHH',
//             'flight_time' => "1:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RCKH',
//             'flight_time' => "1:15"
//         ],
//         [
//             'origin' => 'RCKH',
//             'destination' => 'VHHH',
//             'flight_time' => "1:20"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RCMQ',
//             'flight_time' => "1:20"
//         ],
//         [
//             'origin' => 'RCMQ',
//             'destination' => 'VHHH',
//             'flight_time' => "1:25"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RKSS',
//             'flight_time' => "3:10"
//         ],
//         [
//             'origin' => 'RKSS',
//             'destination' => 'VHHH',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RKSI',
//             'flight_time' => "3:10"
//         ],
//         [
//             'origin' => 'RKSI',
//             'destination' => 'VHHH',
//             'flight_time' => "3:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RKPK',
//             'flight_time' => "2:55"
//         ],
//         [
//             'origin' => 'RKPK',
//             'destination' => 'VHHH',
//             'flight_time' => "3:25"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RKPC',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'RKPC',
//             'destination' => 'VHHH',
//             'flight_time' => "3:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VTBD',
//             'flight_time' => "2:40"
//         ],
//         [
//             'origin' => 'VTBD',
//             'destination' => 'VHHH',
//             'flight_time' => "2:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VTSP',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VTSP',
//             'destination' => 'VHHH',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VTCC',
//             'flight_time' => "2:40"
//         ],
//         [
//             'origin' => 'VTCC',
//             'destination' => 'VHHH',
//             'flight_time' => "2:15"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VTSM',
//             'flight_time' => "3:10"
//         ],
//         [
//             'origin' => 'VTSM',
//             'destination' => 'VHHH',
//             'flight_time' => "2:55"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VTSG',
//             'flight_time' => "3:25"
//         ],
//         [
//             'origin' => 'VTSG',
//             'destination' => 'VHHH',
//             'flight_time' => "3:10"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RJAA',
//             'flight_time' => "3:55"
//         ],
//         [
//             'origin' => 'RJAA',
//             'destination' => 'VHHH',
//             'flight_time' => "4:30"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RJBB',
//             'flight_time' => "3:20"
//         ],
//         [
//             'origin' => 'RJBB',
//             'destination' => 'VHHH',
//             'flight_time' => "3:50"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RJFF',
//             'flight_time' => "2:55"
//         ],
//         [
//             'origin' => 'RJFF',
//             'destination' => 'VHHH',
//             'flight_time' => "3:20"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RJGG',
//             'flight_time' => "3:30"
//         ],
//         [
//             'origin' => 'RJGG',
//             'destination' => 'VHHH',
//             'flight_time' => "4:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ROAH',
//             'flight_time' => "2:05"
//         ],
//         [
//             'origin' => 'ROAH',
//             'destination' => 'VHHH',
//             'flight_time' => "2:25"
//         ],
//         [
//             'origin' => 'RJCC',
//             'destination' => 'VHHH',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RJCC',
//             'flight_time' => "5:10"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZBAA',
//             'flight_time' => "2:55"
//         ],
//         [
//             'origin' => 'ZBAA',
//             'destination' => 'VHHH',
//             'flight_time' => "2:55"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZSPD',
//             'flight_time' => "1:35"
//         ],
//         [
//             'origin' => 'ZSPD',
//             'destination' => 'VHHH',
//             'flight_time' => "2:20"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZSHC',
//             'flight_time' => "1:25"
//         ],
//         [
//             'origin' => 'ZSHC',
//             'destination' => 'VHHH',
//             'flight_time' => "2:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZHHH',
//             'flight_time' => "1:20"
//         ],
//         [
//             'origin' => 'ZHHH',
//             'destination' => 'VHHH',
//             'flight_time' => "1:30"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZJHK',
//             'flight_time' => "1:05"
//         ],
//         [
//             'origin' => 'ZJHK',
//             'destination' => 'VHHH',
//             'flight_time' => "1:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZUCK',
//             'flight_time' => "1:50"
//         ],
//         [
//             'origin' => 'ZUCK',
//             'destination' => 'VHHH',
//             'flight_time' => "1:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'ZUUU',
//             'flight_time' => "2:15"
//         ],
//         [
//             'origin' => 'ZUUU',
//             'destination' => 'VHHH',
//             'flight_time' => "2:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WIHH',
//             'flight_time' => "4:40"
//         ],
//         [
//             'origin' => 'WIHH',
//             'destination' => 'VHHH',
//             'flight_time' => "4:30"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WIII',
//             'flight_time' => "4:40"
//         ],
//         [
//             'origin' => 'WIII',
//             'destination' => 'VHHH',
//             'flight_time' => "4:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WADD',
//             'flight_time' => "4:40"
//         ],
//         [
//             'origin' => 'WADD',
//             'destination' => 'VHHH',
//             'flight_time' => "4:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WARR',
//             'flight_time' => "4:50"
//         ],
//         [
//             'origin' => 'WARR',
//             'destination' => 'VHHH',
//             'flight_time' => "4:45"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WAHI',
//             'flight_time' => "4:45"
//         ],
//         [
//             'origin' => 'WAHI',
//             'destination' => 'VHHH',
//             'flight_time' => "4:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WMSA',
//             'flight_time' => "3:45"
//         ],
//         [
//             'origin' => 'WMSA',
//             'destination' => 'VHHH',
//             'flight_time' => "3:50"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WBKK',
//             'flight_time' => "3:00"
//         ],
//         [
//             'origin' => 'WBKK',
//             'destination' => 'VHHH',
//             'flight_time' => "3:00"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WMKL',
//             'flight_time' => "3:35"
//         ],
//         [
//             'origin' => 'WMKL',
//             'destination' => 'VHHH',
//             'flight_time' => "3:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WMKP',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'WMKP',
//             'destination' => 'VHHH',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VVNB',
//             'flight_time' => "1:40"
//         ],
//         [
//             'origin' => 'VVNB',
//             'destination' => 'VHHH',
//             'flight_time' => "1:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VVTS',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VVTS',
//             'destination' => 'VHHH',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VVDN',
//             'flight_time' => "1:50"
//         ],
//         [
//             'origin' => 'VVDN',
//             'destination' => 'VHHH',
//             'flight_time' => "1:50"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VVPQ',
//             'flight_time' => "2:40"
//         ],
//         [
//             'origin' => 'VVPQ',
//             'destination' => 'VHHH',
//             'flight_time' => "2:30"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'WSSL',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'WSSL',
//             'destination' => 'VHHH',
//             'flight_time' => "3:40"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VDPP',
//             'flight_time' => "2:30"
//         ],
//         [
//             'origin' => 'VDPP',
//             'destination' => 'VHHH',
//             'flight_time' => "2:15"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VDSR',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VDSR',
//             'destination' => 'VHHH',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'VDSV',
//             'flight_time' => "2:50"
//         ],
//         [
//             'origin' => 'VDSV',
//             'destination' => 'VHHH',
//             'flight_time' => "2:50"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RPLL',
//             'flight_time' => "1:45"
//         ],
//         [
//             'origin' => 'RPLL',
//             'destination' => 'VHHH',
//             'flight_time' => "1:55"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RPVM',
//             'flight_time' => "2:20"
//         ],
//         [
//             'origin' => 'RPVM',
//             'destination' => 'VHHH',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RPVP',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'RPVP',
//             'destination' => 'VHHH',
//             'flight_time' => "2:35"
//         ],
//         [
//             'origin' => 'VHHH',
//             'destination' => 'RPLC',
//             'flight_time' => "1:40"
//         ],
//         [
//             'origin' => 'RPLC',
//             'destination' => 'VHHH',
//             'flight_time' => "1:40"
//         ],
//     ];

//     foreach ($data as $key => $value) {
//         $origin = Airport::where(['icao' => $value['origin']])->first();
//         $destination = Airport::where(['icao' => $value['destination']])->first();

//         if ($origin && $destination) {
//             $input = [
//                 "aircraft_id" => 143,
//                 "origin_id" => $origin->id,
//                 "destination_id" => $destination->id,
//                 "flight_time" => $value['flight_time'],
//                 'created_by' => 2,
//                 'updated_by' => 2
//             ];

//             FlightTime::create($input);

//             echo $key . " value inserted!<br />";
//         } else {
//             echo "Origin: " . $value['origin'] . " & Destination: " . $value['destination'] . " value not inserted!<br />";
//         }
//     }
// });
