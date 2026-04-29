<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Jobs\SendNewsletterJob;

class NewsletterController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber) {
            SendNewsletterJob::dispatch(
                $subscriber->email,
                $request->title,
                $request->content
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Newsletter en cours d’envoi à tous les abonnés'
        ]);
    }
}