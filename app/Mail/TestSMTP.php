<?php

namespace App\Mail;

use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestSMTP extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'info@voxmind.com',
            subject: 'SMTP Test From VoxMind',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $settings = Settings::whereIn('key', ['site_title', 'smtp_username'])->pluck('value', 'key');
        return new Content(
            view: 'email.smtp_test',
            with: [
                'smtp_username' => $settings['smtp_username'],
                'site_title' => $settings['site_title']
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
