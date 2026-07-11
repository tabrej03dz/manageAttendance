<?php

namespace App\Mail;

use App\Models\Policy;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PolicyAcceptanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Policy $policy;
    public User $user;

    public function __construct(Policy $policy, User $user)
    {
        $this->policy = $policy;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Policy Accepted Successfully',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.policy_acceptance_notification',
            with: [
                'policy' => $this->policy,
                'user' => $this->user,
            ],
        );
    }
}