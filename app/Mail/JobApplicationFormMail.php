<?php
// app/Mail/JobApplicationFormMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        $poste = $this->formData['metadata']['poste_label'] ?? $this->formData['poste_souhaite'];
        $nom = $this->formData['prenom'] . ' ' . $this->formData['nom'];
        
        return new Envelope(
            subject: '🎯 Nouvelle candidature : ' . $nom . ' — ' . $poste,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application-form',
        );
    }
}