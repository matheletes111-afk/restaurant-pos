<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plan;
    public $subscription;
    public $payment;
    public $restaurant;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $plan, $subscription, $payment, $restaurant)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->subscription = $subscription;
        $this->payment = $payment;
        $this->restaurant = $restaurant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Activated Successfully - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_success',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}