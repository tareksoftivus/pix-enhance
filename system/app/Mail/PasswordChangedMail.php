<?php

namespace App\Mail;

use App\Modules\Shared\Traits\ResolvesRealHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, ResolvesRealHost, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Authenticatable $user,
        public string $ipAddress,
    ) {
        $this->captureRootUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your password was changed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->bindRootUrl();

        return new Content(
            view: 'emails.password-changed',
            with: [
                'changedAt' => now()->format('M d, Y \a\t h:i A'),
                'resetUrl' => route('password.request'),
            ],
        );
    }
}
