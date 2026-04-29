<?php
// app/Http/Controllers/Api/InscriptionMController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInscriptionRequest;
use App\Mail\InscriptionFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InscriptionMController extends Controller
{
    public function store(StoreInscriptionRequest $request)
    {
        // Récupération et préparation des données
        $data = $request->validated();
        
        $formData = [
            'parent_nom' => $data['parentNom'],
            'telephone' => $data['telephone'],
            'email' => $data['email'] ?? null,
            'eleve_nom' => $data['eleveNom'],
            'date_naissance' => $data['dateNaissance'],
            'etablissement' => $data['etablissement'],
            'niveau' => $data['niveau'],
            'message' => $data['message'] ?? null,
            'ip_address' => request()->ip(),
            'statut' => 'en_attente',
        ];

        try {
            // Envoi de l'email à l'administrateur
            Mail::to(config('mail.admin_email', 'stagiaireatome@gmail.com'))
                ->send(new InscriptionFormMail($formData));

            // Optionnel : envoyer une confirmation à l'utilisateur
            // Mail::to($formData['email'])->send(new InscriptionFormMail($formData));

            return response()->json([
                'message' => 'Inscription envoyée avec succès',
                // 'data' => $formData // Optionnel : retourner les données si nécessaire
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email inscription : ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email d\'inscription'
            ], 500);
        }
    }
}