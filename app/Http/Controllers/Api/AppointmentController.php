<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use App\Mail\AppointmentConfirmed;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::query();

        // 🔍 Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('lieu')) {
            $query->where('lieu', $request->lieu);
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
        
        // 📅 Filtre par date de RDV
        if ($request->filled('date_debut')) {
            $query->whereDate('date_rdv', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_rdv', '<=', $request->date_fin);
        }
        
        // 📅 Filtre "Aujourd'hui"
        if ($request->boolean('aujourdhui')) {
            $query->whereDate('date_rdv', today());
        }

        // 📊 Tri
        $sortBy = $request->get('sort_by', 'date_rdv');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSorts = ['date_rdv', 'heure_rdv', 'created_at', 'priorite', 'statut', 'nom'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            // Tri par défaut : date + heure ascendantes
            $query->orderBy('date_rdv', 'asc')->orderBy('heure_rdv', 'asc');
        }

        // 📄 Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $appointments = $query->with('admin')->paginate($perPage);

        // 🎨 Formatage pour le frontend
        $appointments->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'visiteur' => [
                    'nom' => $item->nom,
                    'email' => $item->email,
                    'telephone' => $item->telephone,
                    'invites' => $item->invites ? explode(',', $item->invites) : [],
                ],
                'message' => $item->message,
                'rdv' => [
                    'date' => $item->date_rdv?->format('Y-m-d'),
                    'date_fr' => $item->date_rdv?->format('d/m/Y'),
                    'date_complete' => $item->date_rdv?->translatedFormat('l d F Y'),
                    'heure' => $item->heure_rdv?->format('H:i'),
                    'duree' => $item->duree_minutes,
                    'lieu' => $item->lieu,
                    'datetime_iso' => $item->date_rdv?->setTimeFromTimeString($item->heure_rdv)?->toISOString(),
                ],
                'statut' => $item->statut,
                'priorite' => $item->priorite,
                'note_admin' => $item->note_admin,
                'confirme_le' => $item->confirme_le?->format('d/m/Y H:i'),
                'confirme_par' => $item->admin?->name ?? null,
                'source' => $item->source,
                'ip_address' => $item->ip_address,
                'metadata' => $item->metadata,
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'updated_at' => $item->updated_at?->format('d/m/Y H:i'),
                // Helpers
                'is_pending' => $item->statut === 'pending',
                'is_confirmed' => $item->statut === 'confirmed',
                'is_today' => $item->date_rdv?->isToday(),
                'is_past' => $item->date_rdv?->isPast() && $item->statut !== 'completed',
                'can_confirm' => $item->statut === 'pending',
                'can_cancel' => in_array($item->statut, ['pending', 'confirmed']),
            ];
        });

        return response()->json([
            'data' => $appointments->items(),
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
                'last_page' => $appointments->lastPage(),
                'has_more' => $appointments->hasMorePages(),
                'pending_count' => Appointment::where('statut', 'pending')->count(),
                'today_count' => Appointment::whereDate('date_rdv', today())->where('statut', '!=', 'cancelled')->count(),
            ],
            'filters' => [
                'statuts' => ['pending', 'confirmed', 'cancelled', 'rejected', 'completed'],
                'priorites' => ['faible', 'normale', 'haute'],
                'lieux' => ['Lissasfa'], // À étendre pour multi-sites
            ]
        ]);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        try {
            if (!Appointment::isSlotAvailable($data['date_rdv'], $data['heure_rdv'], $data['lieu'] ?? 'Lissasfa')) {
                return response()->json([
                    'message' => 'Ce créneau vient d\'être réservé. Veuillez choisir un autre horaire.',
                    'available' => false
                ], 409);
            }

            $appointment = Appointment::create([
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
            ]);

            Log::info('Nouveau rendez-vous réservé', [
                'id' => $appointment->id,
                'email' => $appointment->email,
                'rdv' => $appointment->date_rdv . ' ' . $appointment->heure_rdv,
            ]);

            return response()->json([
                'message' => 'Rendez-vous réservé avec succès',
                'data' => [
                    'id' => $appointment->id,
                    'date_rdv' => $appointment->date_rdv->format('Y-m-d'),
                    'heure_rdv' => $appointment->heure_rdv->format('H:i'),
                    'lieu' => $appointment->lieu,
                    'statut' => $appointment->statut,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur réservation RDV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la réservation'
            ], 500);
        }
    }

    /**
     * Update appointment status or admin note.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'statut' => ['nullable', 'in:pending,confirmed,cancelled,rejected,completed'],
            'priorite' => ['nullable', 'in:faible,normale,haute'],
            'note_admin' => ['nullable', 'string', 'max:2000'],
            'envoyer_confirmation' => ['nullable', 'boolean'],
        ]);

        $updateData = [];
        
        if ($request->filled('statut')) {
            $updateData['statut'] = $request->statut;
            if (in_array($request->statut, ['confirmed', 'completed'])) {
                $updateData['confirme_le'] = now();
                if (Auth::check()) {
                    $updateData['confirme_par'] = Auth::id();
                }
            }
        }
        if ($request->filled('priorite')) {
            $updateData['priorite'] = $request->priorite;
        }
        if ($request->filled('note_admin')) {
            $updateData['note_admin'] = $request->note_admin;
        }

        $appointment->update($updateData);

        // 📧 Optionnel : Email de confirmation
        if ($request->boolean('envoyer_confirmation') && $request->statut === 'confirmed') {
            // Mail::to($appointment->email)->send(new AppointmentConfirmed($appointment));
            Log::info('Email de confirmation envoyé', ['appointment_id' => $appointment->id]);
        }

        return response()->json([
            'message' => 'Rendez-vous mis à jour',
            'data' => $appointment->refresh()
        ]);
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        Log::info('Rendez-vous supprimé', ['id' => $appointment->id]);
        return response()->json(['message' => 'Rendez-vous supprimé']);
    }

    /**
     * Vérifier la disponibilité d'un créneau.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'lieu' => ['nullable', 'string', 'max:100'],
        ]);

        $available = Appointment::isSlotAvailable(
            $request->date,
            $request->time,
            $request->lieu ?? 'Lissasfa'
        );

        return response()->json([
            'available' => $available,
            'date' => $request->date,
            'time' => $request->time
        ]);
    }
}