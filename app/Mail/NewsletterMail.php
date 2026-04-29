<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $title;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.newsletter')
            ->with([
                'content' => $this->content,
                'title' => $this->title
            ]);
    }
}
