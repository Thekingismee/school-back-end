<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInscriptionRequest;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 🔐 Optionnel : protéger par middleware 'auth' si réservé aux admins
        // if (!Auth::check()) { return response()->json(['message' => 'Non autorisé'], 403); }

        $query = Inscription::query();

        // 🔍 Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('etablissement')) {
            $query->where('etablissement', $request->etablissement);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('parent_nom', 'like', "%{$search}%")
                  ->orWhere('eleve_nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // 📊 Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // 📄 Pagination
        $perPage = $request->get('per_page', 20);
        $inscriptions = $query->with('admin')->paginate($perPage);

        // 🎨 Formatage pour le frontend
        $inscriptions->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'parent' => [
                    'nom' => $item->parent_nom,
                    'telephone' => $item->telephone,
                    'email' => $item->email,
                ],
                'eleve' => [
                    'nom' => $item->eleve_nom,
                    'date_naissance' => $item->date_naissance?->format('d/m/Y'),
                    'age' => $item->date_naissance?->age ?? null,
                    'etablissement' => $item->etablissement,
                    'niveau' => $item->niveau,
                ],
                'message' => $item->message,
                'statut' => $item->statut,
                'priorite' => $item->priorite,
                'note_admin' => $item->note_admin,
                'date_contact' => $item->date_contact?->format('d/m/Y H:i'),
                'source' => $item->source,
                'ip_address' => $item->ip_address,
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'updated_at' => $item->updated_at?->format('d/m/Y H:i'),
                'traite_par' => $item->admin?->name ?? null,
            ];
        });

        return response()->json([
            'data' => $inscriptions->items(),
            'pagination' => [
                'current_page' => $inscriptions->currentPage(),
                'per_page' => $inscriptions->perPage(),
                'total' => $inscriptions->total(),
                'last_page' => $inscriptions->lastPage(),
                'has_more' => $inscriptions->hasMorePages(),
            ],
            'filters' => [
                'statuts' => ['en_attente', 'contacte', 'accepte', 'refuse'],
                'priorites' => ['faible', 'normale', 'haute'],
                'etablissements' => ['maternelle', 'primaire', 'college', 'lycee'],
            ]
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreInscriptionRequest $request)
    {
        $data = $request->validated();

        $inscription = Inscription::create([
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
        ]);

        return response()->json([
            'message' => 'Inscription envoyée avec succès',
            'data' => $inscription
        ], 201);
    }

    /**
     * Update the status of an inscription.
     */
    public function updateStatus(Request $request, Inscription $inscription)
    {
        $request->validate([
            'statut' => ['required', 'in:en_attente,contacte,accepte,refuse'],
            'note_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $inscription->update([
            'statut' => $request->statut,
            'note_admin' => $request->note_admin ?? $inscription->note_admin,
            'date_contact' => now(),
            'traite_par' => Auth::id(), // Optionnel : nécessite auth
        ]);

        return response()->json([
            'message' => 'Statut mis à jour',
            'data' => $inscription->refresh()
        ]);
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy(Inscription $inscription)
    {
        $inscription->delete(); // Soft delete

        return response()->json(['message' => 'Inscription supprimée']);
    }
}