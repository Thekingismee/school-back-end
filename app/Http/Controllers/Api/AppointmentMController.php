<?php
// app/Http/Controllers/Api/AppointmentMController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppointmentMController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        try {
            // ✅ CONSERVER la vérification de disponibilité (logique métier)
            if (!\App\Models\Appointment::isSlotAvailable(
                $data['date_rdv'], 
                $data['heure_rdv'], 
                $data['lieu'] ?? 'Lissasfa'
            )) {
                return response()->json([
                    'message' => 'Ce créneau vient d\'être réservé. Veuillez choisir un autre horaire.',
                    'available' => false
                ], 409);
            }

            // 📦 Préparation des données pour l'email
            $formData = [
                'nom' => $data['nom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'invites' => $data['invites'] ?? null,
                'message' => $data['message'] ?? null,
                'date_rdv' => $data['date_rdv'],
                'heure_rdv' => $data['heure_rdv'],
                'duree_minutes' => $data['duree_minutes'] ?? 15,
                'lieu' => $data['lieu'] ?? 'Lissasfa',
                'source' => $request->header('X-Source') ?? 'site',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'statut' => 'pending',
                'priorite' => 'normale',
                'metadata' => [
                    'browser' => $request->userAgent(),
                    'platform' => $request->header('User-Platform') ?? 'web',
                ],
            ];

            // 📧 Envoi de l'email à l'administrateur
            Mail::to(config('mail.admin_email', 'stagiaireatome@gmail.com'))
                ->send(new AppointmentFormMail($formData));

            // 📧 Optionnel : confirmation au client
            // Mail::to($formData['email'])->send(new AppointmentFormMail($formData));

            Log::info('Nouveau RDV envoyé par email', [
                'email' => $formData['email'],
                'rdv' => $formData['date_rdv'] . ' ' . $formData['heure_rdv'],
                'lieu' => $formData['lieu'],
            ]);

            return response()->json([
                'message' => 'Rendez-vous réservé avec succès',
                'data' => [
                    // Retourner les données pour affichage frontend sans ID BDD
                    'nom' => $formData['nom'],
                    'date_rdv' => \Carbon\Carbon::parse($formData['date_rdv'])->format('Y-m-d'),
                    'heure_rdv' => $formData['heure_rdv'],
                    'lieu' => $formData['lieu'],
                    'statut' => $formData['statut'],
                    'duree_minutes' => $formData['duree_minutes'],
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur réservation RDV (email)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la réservation'
            ], 500);
        }
    }
}