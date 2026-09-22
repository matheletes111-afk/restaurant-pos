<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\MarketingNotificationRecipient;
use App\Mail\MarketingNotificationMail;

class SendMarketingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipientId;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * Job timeout in seconds.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param int $recipientId
     */
    public function __construct(int $recipientId)
    {
        $this->recipientId = $recipientId;
        // Default to database queue connection for async background delivery
        $this->onConnection(config('queue.default') === 'sync' ? 'database' : config('queue.default'));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipient = MarketingNotificationRecipient::with('notification')->find($this->recipientId);

        if (!$recipient || !$recipient->notification) {
            Log::warning("SendMarketingEmailJob: Recipient or notification not found for ID: {$this->recipientId}");
            return;
        }

        $notification = $recipient->notification;

        // Skip if already sent
        if ($recipient->status === 'sent') {
            return;
        }

        // Validate recipient email
        if (empty($recipient->email) || !filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
            $recipient->update([
                'status' => 'failed',
                'error_message' => 'Invalid or empty email address provided.',
                'sent_at' => now(),
            ]);

            $notification->increment('failed_count');
            $this->checkAndUpdateCampaignStatus($notification);
            return;
        }

        try {
            // Send the email
            Mail::to($recipient->email)->send(new MarketingNotificationMail($notification, $recipient));

            // Mark recipient as sent
            $recipient->update([
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now(),
            ]);

            // Increment sent count
            $notification->increment('sent_count');

        } catch (\Throwable $e) {
            Log::error("SendMarketingEmailJob Failed for recipient {$recipient->id} ({$recipient->email}): " . $e->getMessage());

            $recipient->update([
                'status' => 'failed',
                'error_message' => substr($e->getMessage(), 0, 500),
                'sent_at' => now(),
            ]);

            $notification->increment('failed_count');
        }

        $this->checkAndUpdateCampaignStatus($notification);
    }

    /**
     * Check if all recipients for this notification are processed and update campaign status.
     */
    protected function checkAndUpdateCampaignStatus($notification)
    {
        $pendingCount = MarketingNotificationRecipient::where('notification_id', $notification->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingCount === 0) {
            $freshNotification = $notification->fresh();
            $finalStatus = ($freshNotification->sent_count > 0) ? 'completed' : 'failed';
            
            $freshNotification->update([
                'status' => $finalStatus,
                'sent_at' => now(),
            ]);
        }
    }
}
