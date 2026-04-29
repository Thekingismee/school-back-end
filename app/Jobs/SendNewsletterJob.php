<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $title;
    public $content;

    public function __construct($email, $title, $content)
    {
        $this->email = $email;
        $this->title = $title;
        $this->content = $content;
    }

    public function handle()
    {
        Mail::to($this->email)
            ->send(new NewsletterMail($this->title, $this->content));
    }
}