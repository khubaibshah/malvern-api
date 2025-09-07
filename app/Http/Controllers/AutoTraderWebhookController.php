<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Jobs\SyncAutoTraderVehicleJob;

class AutoTraderWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $raw = $request->getContent();

        // 1) Verify signature (header names may differ – check AT docs)
        $signatureHeader = $request->header('X-Autotrader-Signature');   // e.g.
        $timestampHeader = $request->header('X-Autotrader-Timestamp');   // e.g.
        $secret = config('services.autotrader.webhook_secret', env('AUTOTRADER_WEBHOOK_SECRET'));

        if (!$this->verified($raw, $timestampHeader, $signatureHeader, $secret)) {
            Log::warning('[AT] Webhook signature verification failed');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = $request->json()->all();
        $eventId = $payload['id'] ?? $request->header('X-Autotrader-Event-Id'); // adjust per docs
        $eventType = $payload['type'] ?? $request->header('X-Autotrader-Event'); // adjust per docs

        // 2) Idempotency – skip if seen
        $already = DB::table('processed_webhooks')->where('provider', 'autotrader')->where('event_id', $eventId)->exists();
        if ($already) {
            return response()->json(['message' => 'Already processed'], 200);
        }

        // 3) Log & enqueue
        Log::info('[AT] Webhook received', ['event_id' => $eventId, 'type' => $eventType]);

        // Store before dispatch to avoid race
        DB::table('processed_webhooks')->insert([
            'provider' => 'autotrader',
            'event_id' => (string) $eventId,
            'created_at' => now(),
        ]);

        // Decide what to do based on event type
        switch ($eventType) {
            // Adjust names to match docs, e.g. "vehicle.created", "vehicle.updated", "vehicle.deleted", "media.updated"
            default:
                // Often the payload includes a registration or stock id; if not, pull it from the body fields per docs
                $registration = data_get($payload, 'data.vehicle.registration');
                $stockId      = data_get($payload, 'data.vehicle.id');

                // Kick off your existing code path that fetches *fresh* data from AT API and updates your DB + queues images
                SyncAutoTraderVehicleJob::dispatch($registration, $stockId);
                break;
        }

        // 4) Respond quickly
        return response()->json(['message' => 'OK'], 200);
    }

    private function verified(string $raw, ?string $timestamp, ?string $signature, string $secret): bool
    {
        if (!$timestamp || !$signature || !$secret) return false;

        // Common pattern: HMAC over "<timestamp>.<rawBody>"
        $signedPayload = $timestamp . '.' . $raw;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $signature);
    }
}
