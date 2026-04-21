<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_messages';

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'sujet',
        'message',
        'statut',
        'priorite',
        'source',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
    ];

    // 🔗 Relation avec l'admin qui a traité
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'traite_par');
    }

    // ✅ Scope pour les messages non traités
    public function scopeNouveaux($query)
    {
        return $query->where('statut', 'nouveau');
    }

    // ✅ Scope pour filtrer par sujet
    public function scopeParSujet($query, $sujet)
    {
        return $query->where('sujet', $sujet);
    }
}