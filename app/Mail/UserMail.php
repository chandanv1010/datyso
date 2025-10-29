<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $forgot_url, $username;

    public function __construct($forgot_url, $username)
    {
        $this->forgot_url = $forgot_url;
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->subject('E-Mail Từ Hệ Thống Datyso')
        ->view('admin.resetPasswords.check_email', ['forgot_url', $this->forgot_url, $this->username]);
    }

    /**
     * Get the message content definition.
     */

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
