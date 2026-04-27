<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{


public function me(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Non authentifié.'
        ], 401);
    }

    $user->loadCount(['followers', 'following', 'posts']);

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'pseudo' => $user->pseudo,
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => $user->bio,
            'favorite_meal' => $user->favorite_meal,
            'avatar_url' => $user->avatar_url,
            'role' => $user->role,
            'account_status' => $user->account_status,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at->toIso8601String(),
            'stats' => [
                'followers_count' => $user->followers_count,
                'following_count' => $user->following_count,
                'posts_count' => $user->posts_count,
            ],
        ],
    ]);
}

    /**
     * Display a listing of users with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        // Paramètres de pagination (défaut : 15 par page)
        $perPage = $request->get('per_page', 15);
        $perPage = min(max($perPage, 1), 100); // Limite entre 1 et 100

        // Requête de base
        $query = User::query();

        // 🔍 Filtre optionnel par nom ou email
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 📅 Filtre optionnel par date de création
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->date('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->date('to_date'));
        }

        // 🔄 Tri optionnel
        $allowedSorts = ['id', 'name', 'email', 'created_at'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, $allowedSorts) && in_array($sortDir, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        // 📄 Pagination
        $users = $query->paginate($perPage);

        // 🎁 Formatage de la réponse
        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès',
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'total_pages' => $users->lastPage(),
                'has_more' => $users->hasMorePages(),
                'links' => [
                    'first' => $users->url(1),
                    'last' => $users->url($users->lastPage()),
                    'prev' => $users->previousPageUrl(),
                    'next' => $users->nextPageUrl(),
                ]
            ]
        ], 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'data' => new UserResource($user)
        ], 201);
    }
}