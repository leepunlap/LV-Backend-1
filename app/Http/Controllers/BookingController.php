<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\AircraftImage;
use App\Models\BillingDetail;
use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $billingInformation = $request->billingInformation;
            $selectedFlight = $request->selectedFlight;
            $passengerDetails = $request->passengerDetails;
            $paymentIntent = $request->paymentIntent;
            $flightDate = Carbon::parse($request->selectedFlight['levels'][0]['departureDate'] . ' ' . $request->passengerDetails['time'])->toDateString();
            $flightTime = Carbon::parse($request->selectedFlight['levels'][0]['departureDate'] . ' ' . $request->passengerDetails['time'])->toTimeString();

            DB::beginTransaction();
            $user = User::where(['email' => $passengerDetails['emailAddress']])->first();
            if (!$user) {
                $user = User::create([
                    'email' => $passengerDetails['emailAddress'],
                    'password' => Hash::make(Str::random(10))
                ]);
            }

            $passenger = Passenger::create([
                'users_id' => $user->id,
                'first_name' => $passengerDetails['firstName'],
                'last_name' => $passengerDetails['lastName'],
                'title' => $passengerDetails['title'],
                'email' => $passengerDetails['emailAddress'],
                'phone' => $passengerDetails['phoneNumber'],
                'passport_number' => $passengerDetails['passportNumber'],
                'country_of_issue' => $passengerDetails['countryOfIssue'],
                'expiry_date' => $passengerDetails['firstName'],
                'concierge' => json_encode($passengerDetails['concierge']),
                'request' => $passengerDetails['request'] ?? '-',
                'have_pet' => $passengerDetails['havePet'] ? 1 : 0,
                'type_of_pet' =>  $passengerDetails['typeOfPet'] ?? ''
            ]);

            $billing = BillingDetail::create([
                'passengers_id' => $passenger->id,
                'card_name' => $billingInformation['name'],
                'country' => $billingInformation['country'],
                'city' => $billingInformation['city'],
                'street1' => $billingInformation['street1'],
                'street2' => $billingInformation['street2'],
                'zipcode' => $billingInformation['zipcode'],
            ]);

            $booking = Booking::create([
                'billing_details_id' => $billing->id,
                'passengers_id' => $passenger->id,
                'users_id' => $user->id,
                'date' => $flightDate,
                'time' => $flightTime,
                'aircrafts_id' => $request->selectedFlight['selectedFlight']['item']['equipment']['id'],
                'flight_details' => json_encode($selectedFlight),
                'status' => 'Pending',
            ]);

            Payment::create([
                'payment_id' => $paymentIntent['id'],
                'bookings_id' => $booking->id,
                'users_id' => $user->id,
                'object' => $paymentIntent['object'],
                'amount' => $paymentIntent['amount'],
                'currency' => $paymentIntent['currency'],
                'created' => $paymentIntent['created'],
                'payment_method' => $paymentIntent['payment_method'],
                'status' => $paymentIntent['status'],
                'details' => json_encode($paymentIntent),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking added Successfully!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'message' => 'Booking added Unsuccessfully!'
            ]);
        }
    }

    public function getAllBookings(Request $request)
    {
        $data = Booking::with('user', 'passenger', 'billingDetails', 'payment')->where(['users_id' => auth()->id()])->orderBy('date', 'DESC')->orderBy('time', 'DESC')->get();
        $result = ['previous' => [], 'confirmed' => [], 'reserved' => []];
        foreach ($data as $record) {
            $record->flight_details = json_decode($record->flight_details);
            $aircraft_id = $record->flight_details->selectedFlight->item->equipment->id;
            $record->aircraft = Aircraft::where(['id' => $aircraft_id])->first();
            $record->aircraft->featured_image = AircraftImage::where(['aircraft_id' => $aircraft_id])->first()->name;
            if (Carbon::parse($record->date . ' ' . $record->time)->lt(Carbon::now())) {
                array_push($result['previous'], $record);
            } else {
                if ($record->status == 'Confirmed') {
                    array_push($result['confirmed'], $record);
                } else {
                    array_push($result['reserved'], $record);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved Successfully!',
            'data' => $result,
        ]);
    }

    public function getBookingSchedules(Request $request)
    {
        $data = Booking::with('user', 'passenger', 'billingDetails', 'payment')->where(['users_id' => auth()->id()])->orderBy('date', 'DESC')->orderBy('time', 'DESC')->get();
        $result = ['previous' => [], 'confirmed' => [], 'reserved' => []];
        foreach ($data as $record) {
            $record->flight_details = json_decode($record->flight_details);
            $aircraft_id = $record->flight_details->selectedFlight->item->equipment->id;
            $record->aircraft = Aircraft::where(['id' => $aircraft_id])->first();
            $record->aircraft->featured_image = AircraftImage::where(['aircraft_id' => $aircraft_id])->first()->name;
            if (Carbon::parse($record->date . ' ' . $record->time)->lt(Carbon::now())) {
                array_push($result['previous'], $record);
            } else {
                if ($record->status == 'Confirmed') {
                    array_push($result['confirmed'], $record);
                } else {
                    array_push($result['reserved'], $record);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved Successfully!',
            'data' => $result,
        ]);
    }
}
