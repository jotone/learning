<?php

namespace App\Mail;

use App\Models\{EmailTemplate, Settings, SocialMediaLink, User};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends AbstractMailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected User $user, protected string $token)
    {
        parent::__construct();

        $this->template = EmailTemplate::firstWhere('slug', 'reset-password');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template->name
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.forgot-password',
            with: [
                'reset_url' => route('reset.index', $this->token),
                'content' => $this->applyVariables([
                    'body' => $this->template->body,
                    'footer_text' => $this->template->footer_text,
                    'subject' => $this->template->subject
                ]),
                'settings' => $this->settings,
                'social_links' => SocialMediaLink::select(['type', 'url'])->orderBy('position')->get(),
                'variables' => $this->template->variables
            ]
        );
    }
}
