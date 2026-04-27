<?php

namespace App\Mail;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RevisionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $authorName;

    /**
     * Create a new message instance.
     */
    public function __construct(Paper $paper, $authorName = null)
    {
        $this->paper = $paper;
        $this->authorName = $authorName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Submission of Revised Abstract – DATICAN Conference',
            cc: ['aribisala@uchicago.edu'],  
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.revision-request',
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