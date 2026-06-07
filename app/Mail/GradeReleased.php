<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradeReleased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $courseTitle,
        public string $assignmentTitle,
        public string $score,
        public ?string $feedback,
        public mixed $courseId = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Grade for {$this->assignmentTitle} Has Been Released",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.grade-released',
        );
    }
}
