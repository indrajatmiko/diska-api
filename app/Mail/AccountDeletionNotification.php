<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Kita hanya perlu nama dan email, karena objek User sudah dihapus
    public function __construct(
        public string $userName,
        public string $userEmail
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Penghapusan Akun',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion',
        );
    }
}