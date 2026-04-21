<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'appointments';

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'invites',
        'message',
        'date_rdv',
        'heure_rdv',
        'duree_minutes',
        'lieu',
        'statut',
        'priorite',
        'note_admin',
        'source',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'date_rdv' => 'date',
        'heure_rdv' => 'datetime:H:i',
        'confirme_le' => 'datetime',
        'metadata' => 'array',
    ];

    // 🔗 Relations
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirme_par');
    }

    // ✅ Scopes utiles
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('statut', 'confirmed');
    }

    public function scopeParDate($query, $date)
    {
        return $query->whereDate('date_rdv', $date);
    }

    public function scopeParLieu($query, $lieu)
    {
        return $query->where('lieu', $lieu);
    }

    // 🔍 Vérifier si un créneau est disponible
    public static function isSlotAvailable($date, $time, $lieu = 'Lissasfa')
    {
        return !self::where('date_rdv', $date)
                    ->where('heure_rdv', $time)
                    ->where('lieu', $lieu)
                    ->where('statut', '!=', 'cancelled')
                    ->exists();
    }

    // 🔄 Méthodes métier
    public function confirmer($adminId = null)
    {
        return $this->update([
            'statut' => 'confirmed',
            'confirme_le' => now(),
            'confirme_par' => $adminId,
        ]);
    }

    public function annuler()
    {
        return $this->update(['statut' => 'cancelled']);
    }

    // 📧 Format pour email de confirmation
    public function getFormattedDateTimeAttribute()
    {
        return $this->date_rdv->format('d/m/Y') . ' à ' . $this->heure_rdv->format('H:i');
    }
}