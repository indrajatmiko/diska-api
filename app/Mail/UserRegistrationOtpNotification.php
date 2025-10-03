<?php
namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationOtpNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otpCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Registrasi Pengguna Baru & Kode Verifikasi',
        );
    }

    public function content(): Content
    {
        // Kita akan membuat view untuk email ini
        return new Content(
            view: 'emails.user-registration-otp',
        );
    }
}