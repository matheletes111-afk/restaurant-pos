<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\WhatsAppEvent;

class WhatsAppWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.whatsapp.verify_token' => 'test_wa_verify_token_123']);
        config(['services.whatsapp.app_secret' => 'test_meta_app_secret_456']);
    }

    public function test_webhook_handshake_succeeds_with_valid_token()
    {
        $response = $this->get('/api/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=test_wa_verify_token_123&hub_challenge=challenge_code_9999');

        $response->assertStatus(200);
        $this->assertEquals('challenge_code_9999', $response->getContent());
    }

    public function test_webhook_handshake_supports_dot_notation_parameters()
    {
        $response = $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=test_wa_verify_token_123&hub.challenge=challenge_code_8888');

        $response->assertStatus(200);
        $this->assertEquals('challenge_code_8888', $response->getContent());
    }

    public function test_webhook_handshake_fails_with_invalid_token()
    {
        $response = $this->get('/api/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=wrong_token&hub_challenge=challenge_code_9999');

        $response->assertStatus(403);
    }

    public function test_webhook_receives_and_stores_incoming_message()
    {
        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'WABA_12345',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '15551234567',
                                    'phone_number_id' => '100200300',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'John Doe'],
                                        'wa_id' => '919876543210',
                                    ]
                                ],
                                'messages' => [
                                    [
                                        'from' => '919876543210',
                                        'id' => 'wamid.HBgMOTExOTg3NjU0MzIxMBUCMRIA',
                                        'timestamp' => '1732700000',
                                        'text' => [
                                            'body' => 'Hello, I want to book a table for tonight.'
                                        ],
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawBody, 'test_meta_app_secret_456');

        $response = $this->call(
            'POST',
            '/api/webhooks/whatsapp',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);
        $this->assertEquals('EVENT_RECEIVED', $response->getContent());

        $this->assertDatabaseHas('whatsapp_events', [
            'wa_message_id' => 'wamid.HBgMOTExOTg3NjU0MzIxMBUCMRIA',
            'event_type' => 'text',
            'sender_phone' => '919876543210',
        ]);
    }

    public function test_webhook_rejects_tampered_signature()
    {
        $rawBody = json_encode(['test' => 'data']);
        $invalidSignature = 'sha256=invalid_hash_signature';

        $response = $this->call(
            'POST',
            '/api/webhooks/whatsapp',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => $invalidSignature,
            ],
            $rawBody
        );

        $response->assertStatus(401);
    }

    public function test_webhook_enforces_idempotency_on_retries()
    {
        $waId = 'wamid.IDEMPOTENCY_TEST_001';
        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'messages' => [
                                    [
                                        'from' => '919876543210',
                                        'id' => $waId,
                                        'type' => 'text',
                                        'text' => ['body' => 'Repeat message']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawBody, 'test_meta_app_secret_456');

        // First delivery
        $res1 = $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
        ], $rawBody);
        $res1->assertStatus(200);

        // Meta Retry delivery
        $res2 = $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
        ], $rawBody);
        $res2->assertStatus(200);

        // Enforce only 1 row exists
        $this->assertEquals(1, WhatsAppEvent::where('wa_message_id', $waId)->count());
    }
}
