<?php

namespace App\Mail;

use App\Models\{EmailTemplate, Settings, SocialMediaLink, User};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends AbstractMailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected User $user)
    {
        parent::__construct();

        $this->template = EmailTemplate::firstWhere('slug', 'registration-email');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Email',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.registration',
            with: [
                'activation_url' => '#',
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
