<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $event = Webhook::constructEvent(
            $payload, $sigHeader, $endpointSecret
        );

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            // Payment successful - save the ticket for the user
            // Example: create Ticket model here
        }

        return response('Webhook Handled', 200);
    }
}
