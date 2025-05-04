<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use DateTime;
use DateTimeZone;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $formattedNow = $now->format('Y-m-d H:i:sP');
        
        $until = new DateTime();
        $until->modify('+6 months');
        $formattedUntil = $until->format('Y-m-d H:i:sP');

        $start_station_id = Station::where('name', $request->start_station)->first()->id;
        $end_station_id = Station::where('name', $request->end_station)->first()->id;

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
            'metadata' => [
                'user_id' => $request->user()->id,
                'train_id' => $request->train_id,
                'start_station' => $start_station_id,
                'end_station' => $end_station_id,
                'platny_od' => $formattedNow,
                'platny_do' => $formattedUntil,
            ],
            'success_url' => 'http://192.168.137.1:8002/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://192.168.137.1:8002/cancel',
        ]);

        return response()->json([
            'url' => $session->url
        ]);
    }
}
