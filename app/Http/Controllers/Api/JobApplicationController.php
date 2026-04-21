<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobApplication::query();

        // 🔍 Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('poste_souhaite')) {
            $query->where('poste_souhaite', $request->poste_souhaite);
        }
        if ($request->filled('disponibilite')) {
            $query->where('disponibilite', $request->disponibilite);
        }
        if ($request->filled('etablissement')) {
            $query->where('etablissement', $request->etablissement);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // 📅 Filtre par date de candidature
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // 📊 Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'priorite', 'statut', 'nom', 'prenom', 'disponibilite'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // 📄 Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $applications = $query->with('recruiter')->paginate($perPage);

        // 🎨 Formatage pour le frontend
        $applications->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'candidat' => [
                    'nom_complet' => $item->nom_complet,
                    'nom' => $item->nom,
                    'prenom' => $item->prenom,
                    'email' => $item->email,
                    'telephone' => $item->telephone,
                    'ville' => $item->ville,
                ],
                'poste' => [
                    'code' => $item->poste_souhaite,
                    'label' => $this->getPosteLabel($item->poste_souhaite, $item->poste_autre),
                    'autre' => $item->poste_autre,
                ],
                'etablissement' => $item->etablissement,
                'contrats' => $item->contrats_selectionnes,
                'disponibilite' => [
                    'code' => $item->disponibilite,
                    'label' => $this->getDisponibiliteLabel($item->disponibilite, $item->disponibilite_autre),
                ],
                'message' => $item->message,
                'message_excerpt' => \Str::limit($item->message, 150),
                
                // 📁 Fichiers avec URLs accessibles
                'fichiers' => [
                    'cv' => [
                        'nom' => $item->cv_original_name,
                        'url' => $item->cv_path ? asset('storage/' . $item->cv_path) : null,
                        'mime' => $item->cv_mime_type,
                        'size' => $this->formatFileSize($item->cv_size),
                    ],
                    'lettre' => $item->lettre_path ? [
                        'nom' => $item->lettre_original_name,
                        'url' => asset('storage/' . $item->lettre_path),
                        'mime' => $item->lettre_mime_type,
                        'size' => $this->formatFileSize($item->lettre_size),
                    ] : null,
                    'diplomes' => collect($item->diplomes ?? [])
                        ->map(fn($doc) => [
                            'nom' => $doc['name'],
                            'url' => asset('storage/' . $doc['path']),
                            'mime' => $doc['mime'],
                            'size' => $this->formatFileSize($doc['size']),
                        ])
                        ->toArray(),
                ],
                
                // 📊 Statut & Priorité
                'statut' => $item->statut,
                'priorite' => $item->priorite,
                'note_recruteur' => $item->note_recruteur,
                'date_premier_contact' => $item->date_premier_contact?->format('d/m/Y H:i'),
                'traite_par' => $item->recruiter?->name ?? null,
                
                // 📈 Métadonnées
                'source' => $item->source,
                'ip_address' => $item->ip_address,
                'metadata' => $item->metadata,
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'created_at_iso' => $item->created_at?->toISOString(),
                'updated_at' => $item->updated_at?->format('d/m/Y H:i'),
                
                // Helpers
                'is_nouveau' => $item->statut === 'nouveau',
                'has_lettre' => !empty($item->lettre_path),
                'has_diplomes' => !empty($item->diplomes),
                'contrat_labels' => $item->contrats_selectionnes,
            ];
        });

        return response()->json([
            'data' => $applications->items(),
            'pagination' => [
                'current_page' => $applications->currentPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'last_page' => $applications->lastPage(),
                'has_more' => $applications->hasMorePages(),
                'nouveau_count' => JobApplication::where('statut', 'nouveau')->count(),
            ],
            'filters' => [
                'statuts' => ['nouveau', 'en_cours', 'contacte', 'entretien', 'accepte', 'refuse', 'archive'],
                'priorites' => ['faible', 'normale', 'haute'],
                'postes' => [
                    'enseignant-maternelle', 'enseignant-primaire', 'enseignant-college', 
                    'enseignant-lycee', 'direction', 'support', 'autre'
                ],
                'disponibilites' => ['immediate', '1mois', 'rentree', 'autre'],
                'etablissements' => ["l'Atome-lissasfa"],
            ]
        ]);
    }

    /**
     * Store a newly created resource.
     */
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

            // 🎯 Transformation des checkbox "contrat" en colonnes booléennes
            $contratData = [
                'contrat_cdi' => (bool) $request->input('contrat_cdi'),
                'contrat_cdd' => (bool) $request->input('contrat_cdd'),
                'contrat_temps_plein' => (bool) $request->input('contrat_temps_plein'),
                'contrat_temps_partiel' => (bool) $request->input('contrat_temps_partiel'),
            ];

            // 🧩 Assemblage des données
            $application = JobApplication::create([
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
            ]);

            Log::info('Nouvelle candidature reçue', [
                'id' => $application->id,
                'email' => $application->email,
                'poste' => $application->poste_souhaite,
                'cv' => $application->cv_original_name,
            ]);

            return response()->json([
                'message' => 'Candidature envoyée avec succès',
                'data' => [
                    'id' => $application->id,
                    'nom_complet' => $application->nom_complet,
                    'poste' => $application->poste_souhaite,
                    'etablissement' => $application->etablissement,
                    'created_at' => $application->created_at->format('d/m/Y H:i'),
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur candidature', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rollback des fichiers en cas d'erreur
            if (isset($cvPath) && Storage::exists($cvPath)) {
                Storage::delete($cvPath);
            }

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'envoi de votre candidature'
            ], 500);
        }
    }

    /**
     * Update application status, priority or recruiter note.
     */
    public function update(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'statut' => ['nullable', 'in:nouveau,en_cours,contacte,entretien,accepte,refuse,archive'],
            'priorite' => ['nullable', 'in:faible,normale,haute'],
            'note_recruteur' => ['nullable', 'string', 'max:2000'],
            'date_premier_contact' => ['nullable', 'date'],
        ]);

        $updateData = [];
        
        if ($request->filled('statut')) {
            $updateData['statut'] = $request->statut;
            if ($request->statut === 'contacte' && !$jobApplication->date_premier_contact) {
                $updateData['date_premier_contact'] = now();
            }
        }
        if ($request->filled('priorite')) {
            $updateData['priorite'] = $request->priorite;
        }
        if ($request->filled('note_recruteur')) {
            $updateData['note_recruteur'] = $request->note_recruteur;
        }
        if ($request->filled('date_premier_contact')) {
            $updateData['date_premier_contact'] = $request->date_premier_contact;
        }
        if (Auth::check()) {
            $updateData['traite_par'] = Auth::id();
        }

        $jobApplication->update($updateData);

        return response()->json([
            'message' => 'Candidature mise à jour',
            'data' => $jobApplication->refresh()
        ]);
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy(JobApplication $jobApplication)
    {
        // Les fichiers sont supprimés automatiquement via le modèle (booted/deleting)
        $jobApplication->delete();
        
        Log::info('Candidature supprimée', ['id' => $jobApplication->id]);

        return response()->json(['message' => 'Candidature supprimée']);
    }

    /**
     * Download a file (CV, lettre, diplôme).
     */
    public function downloadFile(JobApplication $jobApplication, string $type, ?int $diplomeIndex = null)
    {
        $path = null;
        $originalName = null;

        if ($type === 'cv') {
            $path = $jobApplication->cv_path;
            $originalName = $jobApplication->cv_original_name;
        } elseif ($type === 'lettre') {
            $path = $jobApplication->lettre_path;
            $originalName = $jobApplication->lettre_original_name;
        } elseif ($type === 'diplome' && $diplomeIndex !== null) {
            $diplomes = $jobApplication->diplomes ?? [];
            if (isset($diplomes[$diplomeIndex]['path'])) {
                $path = $diplomes[$diplomeIndex]['path'];
                $originalName = $diplomes[$diplomeIndex]['name'];
            }
        }

        if (!$path || !Storage::exists('public/' . $path)) {
            abort(404, 'Fichier non trouvé');
        }

        return Storage::download('public/' . $path, $originalName);
    }

    // 🔧 Helpers privés
    private function getPosteLabel(string $poste, ?string $autre = null): string
    {
        $labels = [
            'enseignant-maternelle' => 'Enseignant(e) Maternelle',
            'enseignant-primaire' => 'Enseignant(e) Primaire',
            'enseignant-college' => 'Enseignant(e) Collège',
            'enseignant-lycee' => 'Enseignant(e) Lycée',
            'direction' => 'Direction / Administration',
            'support' => 'Support & Services',
        ];
        return $poste === 'autre' ? ($autre ?? 'Autre poste') : ($labels[$poste] ?? ucfirst($poste));
    }

    private function getDisponibiliteLabel(string $dispo, ?string $autre = null): string
    {
        $labels = [
            'immediate' => 'Immédiate',
            '1mois' => 'Dans 1 mois',
            'rentree' => 'À la rentrée',
            'autre' => 'Autre',
        ];
        return $dispo === 'autre' ? ($autre ?? 'Autre') : ($labels[$dispo] ?? ucfirst($dispo));
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 1) . ' ' . $units[$pow];
    }
}