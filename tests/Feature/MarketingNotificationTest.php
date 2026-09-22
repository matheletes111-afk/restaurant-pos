<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\MarketingNotification;
use App\Models\MarketingNotificationRecipient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendMarketingEmailJob;
use App\Mail\MarketingNotificationMail;

class MarketingNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or find Super Admin
        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_marketing@billbite.com'],
            [
                'name' => 'Super Admin Test',
                'password' => bcrypt('password123'),
                'role' => 'SA',
                'permissions' => ['marketing_notifications', 'restaurant_master'],
            ]
        );
        $this->admin->permissions = ['marketing_notifications', 'restaurant_master'];
        $this->admin->save();
    }

    /**
     * Test admin can access marketing notifications index and create pages.
     */
    public function test_admin_can_access_marketing_index_and_create()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.marketing.index'));
        $response->assertStatus(200);
        $response->assertSee('Marketing Email Notifications');
        $response->assertSee('Broadcast Campaigns');

        $responseCreate = $this->actingAs($this->admin)->get(route('admin.marketing.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Send Marketing Notification');
        $responseCreate->assertSee('Email Message Body (Free Rich Text Editor)');
    }

    /**
     * Test storing a marketing broadcast creates exactly 1 row in marketing_notifications and dispatches jobs.
     */
    public function test_broadcast_creation_creates_single_campaign_row_and_queues_jobs()
    {
        Queue::fake();

        // Create 3 demo restaurant owners with valid emails
        $user1 = User::create(['name' => 'Owner 1', 'email' => 'owner1@example.com', 'password' => bcrypt('pass'), 'role' => 'RES']);
        $user2 = User::create(['name' => 'Owner 2', 'email' => 'owner2@example.com', 'password' => bcrypt('pass'), 'role' => 'RES']);
        $user3 = User::create(['name' => 'Owner 3', 'email' => 'owner3@example.com', 'password' => bcrypt('pass'), 'role' => 'RES']);

        $rest1 = RestaurantMaster::create(['name' => 'Restro 1', 'owner_id' => $user1->id, 'status' => 'A']);
        $rest2 = RestaurantMaster::create(['name' => 'Restro 2', 'owner_id' => $user2->id, 'status' => 'A']);
        $rest3 = RestaurantMaster::create(['name' => 'Restro 3', 'owner_id' => $user3->id, 'status' => 'A']);

        $campaignData = [
            'title' => 'Big Festive Offer for Restaurant Partners!',
            'description' => '<p>Dear {owner_name}, enjoy our new POS features for {restaurant_name}.</p>',
            'notification_date' => now()->format('Y-m-d\TH:i'),
            'target_type' => 'selected',
            'restaurant_ids' => [$rest1->id, $rest2->id, $rest3->id],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.marketing.store'), $campaignData);
        $response->assertRedirect(route('admin.marketing.index'));

        // Assert only ONE single campaign row is created in marketing_notifications
        $this->assertDatabaseHas('marketing_notifications', [
            'title' => 'Big Festive Offer for Restaurant Partners!',
            'target_type' => 'selected',
            'total_recipients' => 3,
        ]);

        $notification = MarketingNotification::where('title', 'Big Festive Offer for Restaurant Partners!')->first();
        $this->assertNotNull($notification);

        // Assert 3 recipient rows are created linked to this 1 campaign
        $this->assertEquals(3, MarketingNotificationRecipient::where('notification_id', $notification->id)->count());

        // Assert queue jobs were dispatched
        Queue::assertPushed(SendMarketingEmailJob::class, 3);
    }

    /**
     * Test single click / show view displays all recipient statuses for the campaign.
     */
    public function test_campaign_show_and_ajax_status_returns_recipient_breakdown()
    {
        $notification = MarketingNotification::create([
            'title' => 'System Update Announcement',
            'description' => '<p>We are updating the system tonight.</p>',
            'scheduled_at' => now(),
            'status' => 'completed',
            'target_type' => 'all',
            'total_recipients' => 2,
            'sent_count' => 1,
            'failed_count' => 1,
            'created_by' => $this->admin->id,
        ]);

        MarketingNotificationRecipient::create([
            'notification_id' => $notification->id,
            'restaurant_name' => 'Tasty Bites',
            'owner_name' => 'John Doe',
            'email' => 'john@tastybites.com',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        MarketingNotificationRecipient::create([
            'notification_id' => $notification->id,
            'restaurant_name' => 'Spice Garden',
            'owner_name' => 'Jane Smith',
            'email' => 'jane@spicegarden.com',
            'status' => 'failed',
            'error_message' => 'SMTP Connection Timeout',
        ]);

        // Test HTML show page
        $response = $this->actingAs($this->admin)->get(route('admin.marketing.show', $notification->id));
        $response->assertStatus(200);
        $response->assertSee('Tasty Bites');
        $response->assertSee('Spice Garden');
        $response->assertSee('SMTP Connection Timeout');

        // Test AJAX status endpoint for modal click
        $ajaxResponse = $this->actingAs($this->admin)->get(route('admin.marketing.status.ajax', $notification->id));
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
            'notification' => [
                'id' => $notification->id,
                'total_recipients' => 2,
                'sent_count' => 1,
                'failed_count' => 1,
            ]
        ]);
    }

    /**
     * Test scheduled command processes due campaigns.
     */
    public function test_artisan_command_processes_due_scheduled_campaigns()
    {
        Queue::fake();

        $scheduledNotification = MarketingNotification::create([
            'title' => 'Scheduled Morning Announcement',
            'description' => '<p>Good morning restaurant partners!</p>',
            'scheduled_at' => now()->subMinutes(5), // Due now
            'status' => 'scheduled',
            'target_type' => 'all',
            'total_recipients' => 1,
            'sent_count' => 0,
            'failed_count' => 0,
            'created_by' => $this->admin->id,
        ]);

        $recipient = MarketingNotificationRecipient::create([
            'notification_id' => $scheduledNotification->id,
            'restaurant_name' => 'Morning Diner',
            'owner_name' => 'Bob',
            'email' => 'bob@morningdiner.com',
            'status' => 'pending',
        ]);

        $this->artisan('marketing:send-scheduled-emails')
            ->assertExitCode(0);

        Queue::assertPushed(SendMarketingEmailJob::class, 1);
        $this->assertEquals('processing', $scheduledNotification->fresh()->status);
    }

    /**
     * Test send test email endpoint.
     */
    public function test_admin_can_send_test_email()
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->postJson(route('admin.marketing.send-test'), [
            'test_email' => 'admin_tester@billbite.com',
            'title' => 'Test Email Subject',
            'description' => '<p>Testing description body.</p>',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        Mail::assertSent(MarketingNotificationMail::class, function ($mail) {
            return $mail->hasTo('admin_tester@billbite.com') && $mail->title === '[TEST] Test Email Subject';
        });
    }
}
