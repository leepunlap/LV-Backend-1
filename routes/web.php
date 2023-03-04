<?php

use App\Models\Amenity;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    Amenity::create(
        [
            'name' => 'Wifi',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Full-size bathrooms',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Meal Service',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Pet accommodation',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Wide-Screen Televisions',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Ambient Lighting',
            'status' => 1
        ]
    );
    Amenity::create(
        [
            'name' => 'Cabin Crew',
            'status' => 1
        ]
    );
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['signed'])->name('verification.verify');




require __DIR__ . '/auth.php';
