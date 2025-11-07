<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $checkout;

    public function __construct($checkout)
    {
        $this->checkout = $checkout;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $adminEmail = config('mail.admin_email', 'admin@example.com');

        return (new MailMessage)
            ->subject('Payment Cancelled – ' . $this->checkout->meal->name)
            ->cc($adminEmail)
            ->view('emails.payment_cancelled', [
                'checkout' => $this->checkout
            ]);
    }
}
