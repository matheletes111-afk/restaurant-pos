<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * 1. Meta Webhook Verification Handshake (GET)
     * Responds to hub.mode=subscribe, hub.verify_token, and hub.challenge
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        $expectedToken = config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN'));

        if ($mode === 'subscribe' && $token === $expectedToken && $challenge) {
            return response($challenge, 200, ['Content-Type' => 'text/plain']);
        }

        return response('Forbidden', 403);
    }

    /**
     * 2. Incoming WhatsApp Messages & Status Updates (POST)
     */
    public function handle(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Hub-Signature-256');

        if (!$this->verifySignature($rawBody, $signature)) {
            Log::warning('WhatsApp Webhook: Invalid signature received');
            return response('Invalid signature', 401);
        }

        $body = json_decode($rawBody, true);
        if (!$body) {
            return response('EVENT_RECEIVED', 200);
        }

        $entry = $body['entry'][0] ?? null;
        $change = $entry['changes'][0] ?? null;
        $value = $change['value'] ?? null;

        $messages = $value['messages'] ?? [];
        $statuses = $value['statuses'] ?? [];

        try {
            foreach ($messages as $message) {
                $waId = $message['id'] ?? null;
                $type = $message['type'] ?? 'message';
                $from = $message['from'] ?? null;

                if ($waId) {
                    WhatsAppEvent::firstOrCreate(
                        ['wa_message_id' => $waId],
                        [
                            'event_type'   => $type,
                            'sender_phone' => $from,
                            'payload'      => $body,
                        ]
                    );
                }
            }

            foreach ($statuses as $status) {
                $statusId = $status['id'] ?? null;
                $statusVal = $status['status'] ?? 'unknown';
                $recipientId = $status['recipient_id'] ?? null;

                if ($statusId) {
                    $uniqueKey = "status:{$statusId}:{$statusVal}";
                    WhatsAppEvent::firstOrCreate(
                        ['wa_message_id' => $uniqueKey],
                        [
                            'event_type'   => 'status',
                            'sender_phone' => $recipientId,
                            'payload'      => $body,
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to persist WhatsApp webhook: ' . $e->getMessage());
        }

        // Return 200 immediately to acknowledge Meta
        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Verify X-Hub-Signature-256
     */
    protected function verifySignature(string $rawBody, ?string $signatureHeader): bool
    {
        $appSecret = config('services.whatsapp.app_secret', env('META_APP_SECRET'));

        // If no app secret is configured during testing, permit the payload
        if (empty($appSecret)) {
            return true;
        }

        if (empty($signatureHeader) || !str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }

        $expectedDigest = hash_hmac('sha256', $rawBody, $appSecret);
        $actualDigest = substr($signatureHeader, 7);

        return hash_equals($expectedDigest, $actualDigest);
    }
}
