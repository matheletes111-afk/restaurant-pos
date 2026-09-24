<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewQrOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $restaurant;
    public $table;

    /**
     * Create a new message instance.
     *
     * @param mixed $order
     * @param mixed $restaurant
     * @param mixed $table
     */
    public function __construct($order, $restaurant, $table = null)
    {
        $this->order = $order;
        $this->restaurant = $restaurant;
        $this->table = $table;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $orderNo = $this->order->order_id ?? ('#' . $this->order->id);
        $tableName = $this->table ? ($this->table->name ?? 'Table ' . $this->table->id) : 'QR Order';
        $restaurantName = $this->restaurant ? $this->restaurant->name : config('app.name');

        return new Envelope(
            subject: "🔔 New QR Code Order {$orderNo} - {$tableName} | {$restaurantName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_qr_order',
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
