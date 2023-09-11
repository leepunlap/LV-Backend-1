<?php

use App\Http\Controllers\Api\AircraftController;
use App\Http\Controllers\Api\AmenityController;
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
Route::get('get-amenities/{id?}', [CommonController::class, 'getAmenities']);
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
    Route::get('get-multicity-flight-time/{aircraft_id}/{origin_id}/{destination_id}/{leg1_id?}', [FlightTimeController::class, 'get_flight_time']);

    // Amenities
    Route::post('amenities/{id?}', [AmenityController::class, 'manage']);
    Route::delete('amenities/{id}', [AmenityController::class, 'delete']);


});