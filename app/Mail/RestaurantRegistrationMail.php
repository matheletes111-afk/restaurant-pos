<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestaurantRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $restaurant;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $plainPassword, $restaurant)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->restaurant = $restaurant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' - Restaurant Registration Successful',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.restaurant_registration',
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