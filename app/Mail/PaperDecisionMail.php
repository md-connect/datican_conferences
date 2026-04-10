<?php

namespace App\Mail;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaperDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $decision;
    public $decisionNotes;
    public $revisionDeadline;

    /**
     * Create a new message instance.
     */
    public function __construct(Paper $paper, $decision, $decisionNotes = null, $revisionDeadline = null)
    {
        $this->paper = $paper;
        $this->decision = $decision;
        $this->decisionNotes = $decisionNotes;
        $this->revisionDeadline = $revisionDeadline;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'accept' => 'Paper Accepted - DATICAN Conference',
            'accept_with_minor_revision' => 'Paper Accepted with Minor Revisions - DATICAN Conference',
            'accept_with_major_revision' => 'Paper Accepted with Major Revisions - DATICAN Conference',
            'reject' => 'Paper Decision - DATICAN Conference',
        ];

        return new Envelope(
            subject: $subjects[$this->decision] ?? 'Paper Decision - DATICAN Conference',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.paper-decision',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}