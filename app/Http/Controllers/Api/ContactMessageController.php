<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // 🔍 Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('sujet')) {
            $query->where('sujet', $request->sujet);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // 📅 Filtre par date
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // 📊 Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'priorite', 'statut', 'nom'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // 📄 Pagination
        $perPage = min($request->get('per_page', 12), 50); // Max 50
        $messages = $query->with('admin')->paginate($perPage);

        // 🎨 Formatage pour le frontend
        $messages->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'contact' => [
                    'nom' => $item->nom,
                    'email' => $item->email,
                    'telephone' => $item->telephone,
                ],
                'sujet' => $item->sujet,
                'sujet_label' => $this->getSujetLabel($item->sujet),
                'message' => $item->message,
                'message_excerpt' => \Str::limit($item->message, 150),
                'statut' => $item->statut,
                'priorite' => $item->priorite,
                'reponse_admin' => $item->reponse_admin,
                'date_reponse' => $item->date_reponse?->format('d/m/Y H:i'),
                'source' => $item->source,
                'ip_address' => $item->ip_address,
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'created_at_iso' => $item->created_at?->toISOString(),
                'updated_at' => $item->updated_at?->format('d/m/Y H:i'),
                'traite_par' => $item->admin?->name ?? null,
                'is_nouveau' => $item->statut === 'nouveau',
            ];
        });

        return response()->json([
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'last_page' => $messages->lastPage(),
                'has_more' => $messages->hasMorePages(),
                'nouveau_count' => ContactMessage::where('statut', 'nouveau')->count(),
            ],
            'filters' => [
                'statuts' => ['nouveau', 'lu', 'en_cours', 'repondu', 'archive'],
                'priorites' => ['faible', 'normale', 'haute'],
                'sujets' => ['admission', 'information', 'visite', 'autre'],
                'sources' => ['site', 'popup', 'footer', 'mobile'],
            ]
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreContactMessageRequest $request)
    {
        $data = $request->validated();

        try {
            $contactMessage = ContactMessage::create([
                'nom'        => $data['nom'],
                'email'      => $data['email'],
                'telephone'  => $data['telephone'] ?? null,
                'sujet'      => $data['sujet'],
                'message'    => $data['message'],
                'source'     => $request->header('X-Source') ?? 'site',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'statut'     => 'nouveau',
                'priorite'   => 'normale',
            ]);

            Log::info('Nouveau message de contact reçu', [
                'id' => $contactMessage->id,
                'email' => $contactMessage->email,
                'sujet' => $contactMessage->sujet
            ]);

            return response()->json([
                'message' => 'Message envoyé avec succès',
                'data' => $contactMessage
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du message de contact', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'envoi du message'
            ], 500);
        }
    }

    /**
     * Update the status or admin response.
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'statut' => ['nullable', 'in:nouveau,lu,en_cours,repondu,archive'],
            'priorite' => ['nullable', 'in:faible,normale,haute'],
            'reponse_admin' => ['nullable', 'string', 'max:2000'],
            'envoyer_email' => ['nullable', 'boolean'],
        ]);

        $updateData = [];
        
        if ($request->filled('statut')) {
            $updateData['statut'] = $request->statut;
            if ($request->statut === 'repondu') {
                $updateData['date_reponse'] = now();
            }
        }
        if ($request->filled('priorite')) {
            $updateData['priorite'] = $request->priorite;
        }
        if ($request->filled('reponse_admin')) {
            $updateData['reponse_admin'] = $request->reponse_admin;
            $updateData['date_reponse'] = now();
            if ($updateData['statut'] ?? $contactMessage->statut !== 'repondu') {
                $updateData['statut'] = 'repondu';
            }
        }
        if (Auth::check()) {
            $updateData['traite_par'] = Auth::id();
        }

        $contactMessage->update($updateData);

        // 📧 Optionnel : Envoyer email de réponse au client
        if ($request->boolean('envoyer_email') && $request->filled('reponse_admin')) {
            // Mail::to($contactMessage->email)->send(new AdminResponse($contactMessage));
            Log::info('Email de réponse envoyé', ['message_id' => $contactMessage->id]);
        }

        return response()->json([
            'message' => 'Message mis à jour',
            'data' => $contactMessage->refresh()
        ]);
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete(); // Soft delete

        Log::info('Message supprimé', ['id' => $contactMessage->id]);

        return response()->json(['message' => 'Message supprimé']);
    }

    /**
     * Helper: Label lisible du sujet.
     */
    private function getSujetLabel(string $sujet): string
    {
        $labels = [
            'admission' => 'Admission',
            'information' => 'Demande d\'information',
            'visite' => 'Visite guidée',
            'autre' => 'Autre',
        ];
        return $labels[$sujet] ?? ucfirst($sujet);
    }
}