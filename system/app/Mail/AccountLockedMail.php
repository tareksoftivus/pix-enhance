<?php

namespace App\Mail;

use App\Modules\Shared\Traits\ResolvesRealHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountLockedMail extends Mailable implements ShouldQueue
{
    use Queueable, ResolvesRealHost, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $email,
        public string $ip,
        public int $minutes,
        public int $maxAttempts = 5,
    ) {
        $this->captureRootUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account temporarily locked',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->bindRootUrl();

        return new Content(
            view: 'emails.account-locked',
            with: [
                'lockedUntil' => now()->addMinutes($this->minutes)->format('M d, Y \a\t h:i A'),
                'resetUrl' => route('password.request'),
            ],
        );
    }
}
