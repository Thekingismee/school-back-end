<?php
// app/Mail/AppointmentFormMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        $date = \Carbon\Carbon::parse($this->formData['date_rdv'])->format('d/m/Y');
        $heure = $this->formData['heure_rdv'];
        
        return new Envelope(
            subject: '📅 Nouveau RDV : ' . $this->formData['nom'] . ' — ' . $date . ' à ' . $heure,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-form',
        );
    }
}