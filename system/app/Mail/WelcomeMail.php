<?php

namespace App\Mail;

use App\Models\User;
use App\Modules\Shared\Traits\ResolvesRealHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, ResolvesRealHost, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
    ) {
        $this->captureRootUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = setting('site_name', config('app.name', 'Admin Panel'));

        return new Envelope(
            subject: "Welcome to {$appName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->bindRootUrl();

        return new Content(
            view: 'emails.welcome',
            with: [
                'dashboardUrl' => route('user.dashboard'),
            ],
        );
    }
}
