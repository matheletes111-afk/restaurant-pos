<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $content;
    public $recipientData;

    /**
     * Create a new message instance.
     *
     * @param mixed $notification (MarketingNotification or array)
     * @param mixed $recipient (MarketingNotificationRecipient or array)
     */
    public function __construct($notification, $recipient)
    {
        $this->title = is_object($notification) ? $notification->title : ($notification['title'] ?? 'Notification from Bill&Bite');
        
        $rawContent = is_object($notification) ? $notification->description : ($notification['description'] ?? '');

        // Recipient details for merge tags
        $ownerName = is_object($recipient) ? ($recipient->owner_name ?? 'Valued Partner') : ($recipient['owner_name'] ?? 'Valued Partner');
        $restaurantName = is_object($recipient) ? ($recipient->restaurant_name ?? 'Restaurant') : ($recipient['restaurant_name'] ?? 'Restaurant');
        $email = is_object($recipient) ? $recipient->email : ($recipient['email'] ?? '');
        $restaurantId = is_object($recipient) ? ($recipient->restaurant_id ?? '') : ($recipient['restaurant_id'] ?? '');

        $this->recipientData = [
            'owner_name' => $ownerName,
            'restaurant_name' => $restaurantName,
            'email' => $email,
            'restaurant_id' => $restaurantId,
        ];

        // Replace placeholders/merge tags
        $replacements = [
            '{owner_name}' => htmlspecialchars($ownerName),
            '{restaurant_name}' => htmlspecialchars($restaurantName),
            '{email}' => htmlspecialchars($email),
            '{restaurant_id}' => htmlspecialchars($restaurantId),
            '{login_url}' => url('/login'),
            '{portal_url}' => url('/login'),
            '{year}' => date('Y'),
            '{app_name}' => config('app.name', 'Bill&Bite POS'),
        ];

        $this->content = str_replace(array_keys($replacements), array_values($replacements), $rawContent);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.marketing_notification')
                    ->with([
                        'title' => $this->title,
                        'emailContent' => $this->content,
                        'recipientData' => $this->recipientData,
                    ]);
    }
}
