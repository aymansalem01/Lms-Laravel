<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuizGradeReleased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $courseTitle,
        public string $quizTitle,
        public string $score,
        public string $maxScore,
        public mixed $courseId = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Quiz Result for {$this->quizTitle} Has Been Released",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quiz-grade-released',
        );
    }
}
