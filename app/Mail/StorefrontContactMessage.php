<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StorefrontContactMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $contact,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New storefront contact enquiry: '.$this->contact['subject'],
            replyTo: [
                new Address($this->contact['email'], $this->contact['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
        );
    }
}
