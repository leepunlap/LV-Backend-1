<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{

    public function index(Request $request)
    {
        $total_amount = (int) (((float) $request->total_amount) * 100);
        Stripe::setApiKey(env('STRIPESECRETKEY'));

        // Need to remove try catch later
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $total_amount,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (\Throwable $th) {
            $paymentIntent = PaymentIntent::create([
                'amount' => 1231230,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        }

        $output = [
            'clientSecret' => $paymentIntent->client_secret,
        ];

        return response()->json([
            'status' => true,
            'data' => $output
        ]);
    }
}
