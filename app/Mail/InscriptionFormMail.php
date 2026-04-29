<?php
// app/Mail/InscriptionFormMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InscriptionFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle inscription : ' . ($this->formData['eleve_nom'] ?? 'Élève sans nom'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inscription-form',
        );
    }
}