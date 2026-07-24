<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDeleteOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data containing name, email, order details, and OTP code.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = env('APP_NAME') . " - Order Deletion Verification OTP";
        
        return $this->view('emails.order_delete_otp')
                     ->to($this->data['email'])
                     ->subject($subject)
                     ->from(env('MAIL_USERNAME'), env('APP_NAME'));
    }
}
