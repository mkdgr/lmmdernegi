<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Yeni form başvurularını derneğe bildiren sade e-posta. */
class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public array $fields,
        public ?string $panelUrl = null,
        public ?string $replyToEmail = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Web] '.$this->title,
            replyTo: $this->replyToEmail ? [$this->replyToEmail] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-notification');
    }
}
