<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Ticket;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Assuming the session contains user_id and ticket details in metadata
            $userId = $session->metadata->user_id;
            $trainId = $session->metadata->train_id;
            $startStation = $session->metadata->start_station;
            $endStation = $session->metadata->end_station;
            $validFrom = $session->metadata->platny_od;
            $validTo = $session->metadata->platny_do;

            // Create a new ticket for the user
            Ticket::create([
                'user_id' => $userId,
                'train_id' => $trainId,
                'start_station' => $startStation,
                'end_station' => $endStation,
                'platny_od' => $validFrom,
                'platny_do' => $validTo,
            ]);
        }

        return response('Webhook Handled', 200);
    }
}
