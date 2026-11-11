<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;
    public string $customFromAddress;
    public string $customFromName;

    public function __construct(Contact $contact, string $customFromAddress = '', string $customFromName = '')
    {
        $this->contact = $contact;
        $this->customFromAddress = trim($customFromAddress);
        $this->customFromName = trim($customFromName);
    }

    public function envelope(): Envelope
    {
        $sourceLabel = $this->contact->source_name ?: ($this->contact->product?->name ?? '');
        $subject = '[DATYSO Lead] Yêu cầu báo giá';
        if ($sourceLabel !== '') {
            $subject .= ': ' . $sourceLabel;
        }
        $subject .= ' - ' . $this->contact->username . ' (' . $this->contact->phone . ')';

        $from = null;
        if ($this->customFromAddress !== '' && filter_var($this->customFromAddress, FILTER_VALIDATE_EMAIL)) {
            $fromName = $this->customFromName !== '' ? $this->customFromName : (string) config('app.name', 'DATYSO');
            $from = new Address($this->customFromAddress, $fromName);
        }

        $replyTo = [];
        if (!empty($this->contact->email) && filter_var($this->contact->email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address($this->contact->email, $this->contact->username ?: null);
        }

        return new Envelope(
            from: $from,
            replyTo: $replyTo,
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.email.check_email',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
