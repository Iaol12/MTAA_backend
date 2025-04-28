<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Train Ticket',
                    ],
                    'unit_amount' => 500, // $5.00
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://192.168.137.1:8002/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://192.168.137.1:8002/cancel',
        ]);

        return response()->json([
            'url' => $session->url
        ]);
    }
}
