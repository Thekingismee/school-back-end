<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données (adaptée à votre formulaire React)
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'sujet' => 'required|in:admission,information,visite,autre',
            'message' => 'required|string|min:10|max:2000',
        ], [
            'sujet.in' => 'Veuillez sélectionner un sujet valide.',
            'message.min' => 'Votre message doit contenir au moins 10 caractères.',
        ]);

        try {
            // Envoi de l'email à l'administrateur
            Mail::to(config('mail.admin_email', 'stagiaireatome@gmail.com'))
                ->send(new ContactFormMail($validated));

            // Optionnel : envoyer une copie à l'utilisateur
            // Mail::to($validated['email'])->send(new ContactFormMail($validated));

            return response()->json([
                'message' => 'Message envoyé avec succès'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email contact : ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email'
            ], 500);
        }
    }
}