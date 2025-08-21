<?php

namespace App\Mail;

use App\Models\PendaftaranPeserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $peserta;
    public $token;

    public function __construct(PendaftaranPeserta $peserta, $token)
    {
        $this->peserta = $peserta;
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Link Registrasi Akun - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-link',
            with: [
                'peserta' => $this->peserta,
                'registrationUrl' => route('registration.form', $this->token),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}