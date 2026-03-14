<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkspaceInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkspaceInvitation $invitation,
        public Workspace $workspace,
        public ?User $inviter
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join {$this->workspace->name}",
        );
    }

    public function content(): Content
    {
        $baseUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/');
        $acceptUrl = $baseUrl . '/invitations/accept/' . $this->invitation->token;

        return new Content(
            view: 'emails.workspace-invitation',
            with: ['acceptUrl' => $acceptUrl],
        );
    }
}
