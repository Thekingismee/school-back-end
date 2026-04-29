<?php
// app/Http/Controllers/Api/JobApplicationMController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Mail\JobApplicationFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JobApplicationMController extends Controller
{
    public function store(StoreJobApplicationRequest $request)
    {
        $data = $request->validated();

        try {
            // 📁 Gestion du CV (obligatoire)
            $cv = $request->file('cv');
            $cvPath = $cv->store('candidatures/cv', 'public');
            $cvData = [
                'cv_path' => $cvPath,
                'cv_original_name' => $cv->getClientOriginalName(),
                'cv_mime_type' => $cv->getMimeType(),
                'cv_size' => $cv->getSize(),
            ];

            // 📁 Gestion de la lettre (optionnelle)
            $lettreData = [];
            if ($request->hasFile('lettre')) {
                $lettre = $request->file('lettre');
                $lettrePath = $lettre->store('candidatures/lettres', 'public');
                $lettreData = [
                    'lettre_path' => $lettrePath,
                    'lettre_original_name' => $lettre->getClientOriginalName(),
                    'lettre_mime_type' => $lettre->getMimeType(),
                    'lettre_size' => $lettre->getSize(),
                ];
            }

            // 📁 Gestion des diplômes (multiples, optionnels)
            $diplomesData = [];
            if ($request->hasFile('diplomes')) {
                foreach ($request->file('diplomes') as $diplome) {
                    $path = $diplome->store('candidatures/diplomes', 'public');
                    $diplomesData[] = [
                        'name' => $diplome->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $diplome->getMimeType(),
                        'size' => $diplome->getSize(),
                        'uploaded_at' => now(),
                    ];
                }
            }

            // 🎯 Transformation des checkbox "contrat" en booléens
            $contratData = [
                'contrat_cdi' => (bool) $request->input('contrat_cdi'),
                'contrat_cdd' => (bool) $request->input('contrat_cdd'),
                'contrat_temps_plein' => (bool) $request->input('contrat_temps_plein'),
                'contrat_temps_partiel' => (bool) $request->input('contrat_temps_partiel'),
            ];

            // 🧩 Assemblage des données pour l'email
            $formData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'ville' => $data['ville'],
                'etablissement' => $data['etablissement'],
                'poste_souhaite' => $data['poste_souhaite'],
                'poste_autre' => $data['poste_autre'] ?? null,
                ...$contratData,
                ...$cvData,
                ...$lettreData,
                'diplomes' => $diplomesData ?: null,
                'disponibilite' => $data['disponibilite'],
                'disponibilite_autre' => $data['disponibilite_autre'] ?? null,
                'message' => $data['message'] ?? null,
                'source' => $request->header('X-Source') ?? 'site',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'statut' => 'nouveau',
                'priorite' => 'normale',
                'metadata' => [
                    'browser' => $request->userAgent(),
                    'platform' => $request->header('User-Platform') ?? 'web',
                    'poste_label' => $this->getPosteLabel($data['poste_souhaite'], $data['poste_autre'] ?? null),
                ],
            ];

            // 📧 Envoi de l'email à l'administrateur
            Mail::to(config('mail.admin_email', 'stagiaireatome@gmail.com'))
                ->send(new JobApplicationFormMail($formData));

            // 📧 Optionnel : accusé de réception au candidat
            // Mail::to($formData['email'])->send(new JobApplicationFormMail($formData));

            Log::info('Nouvelle candidature envoyée par email', [
                'email' => $formData['email'],
                'poste' => $formData['poste_souhaite'],
                'cv' => $formData['cv_original_name'],
            ]);

            return response()->json([
                'message' => 'Candidature envoyée avec succès',
                'data' => [
                    'nom_complet' => $formData['prenom'] . ' ' . $formData['nom'],
                    'poste' => $formData['metadata']['poste_label'],
                    'etablissement' => $formData['etablissement'],
                    'created_at' => now()->format('d/m/Y H:i'),
                    // Liens vers les fichiers pour affichage frontend si besoin
                    'cv_url' => Storage::url($formData['cv_path']),
                    'lettre_url' => !empty($formData['lettre_path']) ? Storage::url($formData['lettre_path']) : null,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur candidature (email)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // 🗑️ Rollback des fichiers en cas d'erreur
            $this->cleanupUploadedFiles($request);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'envoi de votre candidature'
            ], 500);
        }
    }

    /**
     * Helper : Supprimer les fichiers uploadés en cas d'erreur
     */
    private function cleanupUploadedFiles(Request $request): void
    {
        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->getPathname();
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
        if ($request->hasFile('lettre')) {
            $path = $request->file('lettre')->getPathname();
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
        if ($request->hasFile('diplomes')) {
            foreach ($request->file('diplomes') as $file) {
                $path = $file->getPathname();
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }
    }

    /**
     * Helper : Label du poste (à adapter selon votre logique)
     */
    private function getPosteLabel(string $poste, ?string $autre = null): string
    {
        $labels = [
            'enseignant' => 'Enseignant(e)',
            'educateur' => 'Éducateur(trice)',
            'administratif' => 'Personnel administratif',
            'direction' => 'Direction',
            'autre' => $autre ?? 'Autre poste',
        ];
        return $labels[$poste] ?? $poste;
    }
}