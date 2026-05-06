<?php

namespace App\Mail;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RevisedAbstractReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $authorName;
    public $deadline;

    /**
     * Create a new message instance.
     */
    public function __construct(Paper $paper, $authorName, $deadline = 'May 7, 2026')
    {
        $this->paper = $paper;
        $this->authorName = $authorName;
        $this->deadline = $deadline;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'REMINDER: Revised Abstract Submission - DATICAN Conference',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.revised-abstract-reminder',
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