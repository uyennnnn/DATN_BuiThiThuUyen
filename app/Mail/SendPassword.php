<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $password;

    public $storeName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $storeName)
    {
        $this->user = $user;
        $this->password = $password;
        $this->storeName = $storeName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SpiSpi] Thông báo mật khẩu',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.send-password',
            with: [
                'storeName' => $this->storeName,
                'user' => $this->user,
                'password' => $this->password,
            ],
        );
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
