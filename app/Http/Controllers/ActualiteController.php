<?php
// app/Http/Controllers/ActualiteController.php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ActualiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Actualite::query();

        // 🔍 Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('categorie', 'like', "%{$search}%");
            });
        }
        
        // 📅 Filtre par date de publication
        if ($request->filled('date_debut')) {
            $query->whereDate('date_publication', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_publication', '<=', $request->date_fin);
        }

        // 📊 Tri
        $sortBy = $request->get('sort_by', 'date_publication');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['date_publication', 'created_at', 'categorie', 'titre', 'statut'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // 📄 Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $actualites = $query->paginate($perPage);

        // 🎨 Formatage pour le frontend
        $actualites->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'image' => $item->image ? [
                    'path' => $item->image,
                    'url' => asset('storage/' . $item->image),
                    'thumbnail' => asset('storage/' . $item->image), // Optionnel : générer une vraie miniature
                ] : null,
                'date_publication' => $item->date_publication?->format('Y-m-d'),
                'date_publication_fr' => $item->date_publication?->translatedFormat('d F Y'),
                'categorie' => $item->categorie,
                'titre' => $item->titre,
                'description' => $item->description,
                'description_excerpt' => Str::limit(strip_tags($item->description), 200),
                'slug' => $item->slug,
                'statut' => $item->statut,
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'updated_at' => $item->updated_at?->format('d/m/Y H:i'),
                // Helpers
                'is_publie' => $item->statut === 'publie',
                'is_brouillon' => $item->statut === 'brouillon',
                'has_image' => !empty($item->image),
            ];
        });

        return response()->json([
            'data' => $actualites->items(),
            'pagination' => [
                'current_page' => $actualites->currentPage(),
                'per_page' => $actualites->perPage(),
                'total' => $actualites->total(),
                'last_page' => $actualites->lastPage(),
                'has_more' => $actualites->hasMorePages(),
                'publie_count' => Actualite::where('statut', 'publie')->count(),
                'brouillon_count' => Actualite::where('statut', 'brouillon')->count(),
            ],
            'filters' => [
                'statuts' => ['brouillon', 'publie'],
                'categories' => $this->getCategoriesList(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        // 🔍 Recherche par slug ou ID
        $actualite = Actualite::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$actualite) {
            return response()->json([
                'message' => 'Actualité non trouvée'
            ], 404);
        }

        // 🎨 Formatage détaillé pour le frontend
        $responseData = [
            'id' => $actualite->id,
            'image' => $actualite->image ? [
                'path' => $actualite->image,
                'url' => asset('storage/' . $actualite->image),
                'thumbnail' => asset('storage/' . $actualite->image),
            ] : null,
            'date_publication' => $actualite->date_publication?->format('Y-m-d'),
            'date_publication_fr' => $actualite->date_publication?->translatedFormat('d F Y'),
            'categorie' => $actualite->categorie,
            'titre' => $actualite->titre,
            'description' => $actualite->description,
            'slug' => $actualite->slug,
            'statut' => $actualite->statut,
            'created_at' => $actualite->created_at?->format('d/m/Y H:i'),
            'updated_at' => $actualite->updated_at?->format('d/m/Y H:i'),
            // Helpers
            'is_publie' => $actualite->statut === 'publie',
            'is_brouillon' => $actualite->statut === 'brouillon',
            'has_image' => !empty($actualite->image),
            // Métadonnées SEO
            'meta_title' => $actualite->titre,
            'meta_description' => Str::limit(strip_tags($actualite->description), 160),
        ];

        return response()->json([
            'data' => $responseData
        ]);
    }
    public function store(Request $request)
    {
        // 🔐 Validation des données
        $validated = $request->validate([
            // 🖼️ Image (optionnelle mais recommandée)
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            
            // 📅 Date de publication
            'date_publication' => ['required', 'date'],
            
            // 🏷️ Catégorie
            'categorie' => [
                'required', 
                'string', 
                'max:50',
                Rule::in($this->getCategoriesList())
            ],
            
            // 📰 Titre
            'titre' => ['required', 'string', 'min:5', 'max:255'],
            
            // 📝 Description (contenu riche)
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            
            // 🔗 Slug (optionnel, généré automatiquement si absent)
            'slug' => ['nullable', 'string', 'max:255', 'unique:actualites,slug'],
            
            // 📊 Statut
            'statut' => ['nullable', 'in:brouillon,publie'],
        ], [
            'image.image' => 'Le fichier doit être une image',
            'image.mimes' => 'Formats acceptés : JPEG, PNG, JPG, WebP',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo',
            'categorie.in' => 'Catégorie invalide',
            'titre.min' => 'Le titre doit contenir au moins 5 caractères',
            'description.min' => 'La description doit contenir au moins 20 caractères',
        ]);

        try {
            // 🖼️ Gestion de l'image uploadée
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Nom unique pour éviter les conflits
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                // Stockage dans storage/app/public/actualites/
                $imagePath = $image->storeAs('actualites', $imageName, 'public');
            }

            // 🔗 Génération automatique du slug si non fourni
            $slug = $validated['slug'] ?? Str::slug($validated['titre']);
            
            // Vérifier l'unicité du slug
            $counter = 1;
            $originalSlug = $slug;
            while (Actualite::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // 📦 Création de l'actualité
            $actualite = Actualite::create([
                'image' => $imagePath,
                'date_publication' => $validated['date_publication'],
                'categorie' => $validated['categorie'],
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'slug' => $slug,
                'statut' => $validated['statut'] ?? 'brouillon',
            ]);

            Log::info('Nouvelle actualité créée', [
                'id' => $actualite->id,
                'titre' => $actualite->titre,
            ]);

            // 🎨 Formatage de la réponse
            $responseData = [
                'id' => $actualite->id,
                'image' => $actualite->image ? [
                    'path' => $actualite->image,
                    'url' => asset('storage/' . $actualite->image),
                ] : null,
                'date_publication' => $actualite->date_publication?->format('Y-m-d'),
                'date_publication_fr' => $actualite->date_publication?->translatedFormat('d F Y'),
                'categorie' => $actualite->categorie,
                'titre' => $actualite->titre,
                'slug' => $actualite->slug,
                'statut' => $actualite->statut,
                'created_at' => $actualite->created_at?->format('d/m/Y H:i'),
            ];

            return response()->json([
                'message' => 'Actualité créée avec succès',
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur création actualité', [
                'error' => $e->getMessage(),
            ]);

            // 🔙 Rollback de l'image en cas d'erreur
            if ($imagePath && Storage::exists('public/' . $imagePath)) {
                Storage::delete('public/' . $imagePath);
            }

            return response()->json([
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper : Liste des catégories disponibles.
     * À adapter selon vos besoins métiers.
     */
    private function getCategoriesList(): array
    {
        return [
            'Sport',
            'Commémoration',
            'Vie Scolaire',
            'Culture',
            'Événements',
            'Résultats',
            'Partenariats',
            'Autre',
        ];
    }

    public function getRecent()
    {
        $actualites = Actualite::where('statut', 'publie')
            ->orderBy('date_publication', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($actualite) {
                // Formatage de la date comme dans le composant React : "15 Mars 2024"
                return [
                    'id' => $actualite->id,
                    'image' => $actualite->image,
                    'date' => $actualite->date_publication ? 
                        $actualite->date_publication->translatedFormat('d F Y') : null,
                    'categorie' => $actualite->categorie,
                    'titre' => $actualite->titre,
                    'description' => $actualite->description,
                    'slug' => $actualite->slug,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $actualites,
            'count' => $actualites->count()
        ], 200);
    }
}