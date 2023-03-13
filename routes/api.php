<?php

use App\Http\Controllers\Api\AircraftController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
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
Route::get('get-fleet/{id}', [FleetController::class, 'getFleet']);
Route::get('get-popular-fleets', [FleetController::class, 'getPopularFleets']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('update-profile-picture', [UserController::class, 'updateProfileImage']);
    Route::get('user/profile', function (Request $request) {
        return $request->user();
    });

    Route::get('get-bookings', [BookingController::class, 'getAllBookings']);
    Route::get('get-booking-schedules', [BookingController::class, 'getBookingSchedules']);

    Route::post('fleet/{id}', [AircraftController::class, 'manage']);
    Route::get('fleet', [AircraftController::class, 'index']);
    Route::get('fleet/{id}', [AircraftController::class, 'show']);
    Route::delete('fleet/{id}', [AircraftController::class, 'delete']);
});
