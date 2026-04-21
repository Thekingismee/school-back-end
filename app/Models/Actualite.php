<?php
// app/Models/Actualite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'date_publication',
        'categorie',
        'titre',
        'description',
        'slug',
        'statut',
    ];

    protected $casts = [
        'date_publication' => 'date',
    ];

    // Scope pour n'avoir que les actualités publiées
    public function scopePubliees($query)
    {
        return $query->where('statut', 'publie');
    }
}