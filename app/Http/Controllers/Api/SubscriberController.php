<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email|unique:subscribers,email'
        ]);

        // Création
        $subscriber = Subscriber::create([
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email ajouté avec succès',
            'data' => $subscriber
        ], 201);
    }
}