<?php

namespace App\Http\Controllers;

use App\Models\AdditionalService;

class CustomerController extends Controller
{
    public function index()
    {
        $users = User::where('user_type', 'customer')
            ->withCount('bookings')
            ->with(['bookings.payment'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($user) {
                $user->total_spent = $user->bookings->sum(function ($booking) {
                    return $booking->payment ? (float) $booking->payment->amount : 0;
                });
                unset($user->bookings);
                return $user;
            });

        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully.',
            'data' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::with([
            'bookings.passenger',
            'bookings.payment',
            'bookings.aircraft',
            'bookings.additionalServices',
        ])->findOrFail($id);

        $user->bookings->each(function ($booking) {
            if ($booking->passenger && $booking->passenger->concierge) {
                $booking->passenger->concierge = json_decode($booking->passenger->concierge);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Customer retrieved successfully.',
            'data' => $user,
        ]);
    }
}
