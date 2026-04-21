<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_nom',
        'telephone',
        'email',
        'eleve_nom',
        'date_naissance',
        'etablissement',
        'niveau',
        'message',
        'statut',
        'priorite',
        'note_admin',
        'date_contact',
        'traite_par',
        'source',
        'ip_address',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_contact' => 'datetime',
    ];

    // 🔗 RELATION : Admin qui a traité l'inscription
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'traite_par');
    }

    // ✅ Attribute : Nom complet du parent (pratique pour l'affichage)
    public function getParentNomCompletAttribute()
    {
        return $this->parent_nom;
    }

    // ✅ Attribute : Âge de l'élève calculé
    public function getEleveAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    // ✅ Attribute : Label lisible du statut
    public function getStatutLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'contacte' => 'Contacté',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    // ✅ Attribute : Couleur du statut pour le frontend
    public function getStatutColorAttribute()
    {
        $colors = [
            'en_attente' => 'yellow',
            'contacte' => 'blue',
            'accepte' => 'green',
            'refuse' => 'red',
        ];
        return $colors[$this->statut] ?? 'gray';
    }
}