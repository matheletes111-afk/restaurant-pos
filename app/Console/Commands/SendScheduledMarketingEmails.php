<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarketingNotification;
use App\Models\MarketingNotificationRecipient;
use App\Jobs\SendMarketingEmailJob;
use Illuminate\Support\Facades\Log;

class SendScheduledMarketingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing:send-scheduled-emails {--limit=20 : Max campaigns to process in single run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch background queue jobs for due scheduled marketing notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');

        $dueNotifications = MarketingNotification::whereIn('status', ['scheduled', 'pending'])
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('scheduled_at', 'asc')
            ->take($limit)
            ->get();

        if ($dueNotifications->isEmpty()) {
            $this->info('No due scheduled notifications to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$dueNotifications->count()} due marketing notification(s). Processing...");

        foreach ($dueNotifications as $notification) {
            $this->line("Processing Campaign #{$notification->id}: '{$notification->title}'");

            // Mark as processing
            $notification->update(['status' => 'processing']);

            // Fetch all pending recipients
            $pendingRecipients = MarketingNotificationRecipient::where('notification_id', $notification->id)
                ->where('status', 'pending')
                ->get();

            if ($pendingRecipients->isEmpty()) {
                $notification->update([
                    'status' => ($notification->sent_count > 0) ? 'completed' : 'failed',
                    'sent_at' => now(),
                ]);
                $this->warn("No pending recipients for Campaign #{$notification->id}. Marked as completed.");
                continue;
            }

            $this->info("Dispatching " . $pendingRecipients->count() . " email jobs for Campaign #{$notification->id}...");

            foreach ($pendingRecipients as $recipient) {
                SendMarketingEmailJob::dispatch($recipient->id);
            }

            $this->info("Successfully dispatched jobs for Campaign #{$notification->id}.");
        }

        return Command::SUCCESS;
    }
}
