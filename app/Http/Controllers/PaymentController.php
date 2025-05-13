<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Train;
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

        // Get the user with their discount
        $user = $request->user();
        
        // Base price in cents
        $baseAmount = 500; // $5.00
        $finalAmount = $baseAmount;
        
        // Get the train
        $train = Train::findOrFail($request->train_id);
        $discountApplied = false;
        
        // Apply discount if user has one and train has discounted tickets available
        if ($user->discount_id && $train->discounted_tickets > 0) {
            $discount = $user->discount;
            if ($discount) {
                // Calculate the discounted price (coefficient is percentage of discount)
                $discountPercentage = $discount->coeficient;
                $discountAmount = ($baseAmount * $discountPercentage) / 100;
                $finalAmount = $baseAmount - $discountAmount;
                
                // Ensure minimum amount is at least 1 cent
                $finalAmount = max(1, $finalAmount);
                
                // Decrement the discounted tickets count
                $train->discounted_tickets -= 1;
                $train->save();
                
                $discountApplied = true;
            }
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Train Ticket',
                        'description' => $discountApplied ? 'Discounted price applied' : 'Standard price',
                    ],
                    'unit_amount' => (int)$finalAmount, // Cast to integer to ensure valid amount
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'user_id' => $user->id,
                'train_id' => $request->train_id,
                'start_station' => $start_station_id,
                'end_station' => $end_station_id,
                'platny_od' => $formattedNow,
                'platny_do' => $formattedUntil,
                'discount_id' => $discountApplied ? $user->discount_id : null,
                'discounted_ticket_used' => $discountApplied ? 'yes' : 'no',
            ],
            'success_url' => 'http://192.168.137.1:8002/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://192.168.137.1:8002/cancel',
        ]);

        return response()->json([
            'url' => $session->url
        ]);
    }
}
