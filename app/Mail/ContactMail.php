<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        $sourceLabel = $this->contact->source_name ?: ($this->contact->product?->name ?? '');
        $subject = '[DATYSO Lead] Yêu cầu báo giá';
        if ($sourceLabel !== '') {
            $subject .= ': ' . $sourceLabel;
        }
        $subject .= ' - ' . $this->contact->username . ' (' . $this->contact->phone . ')';

        return $this->subject($subject)
            ->view('admin.email.check_email');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
