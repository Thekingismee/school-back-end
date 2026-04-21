<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_applications';

    protected $fillable = [
        // Personal info
        'nom', 'prenom', 'email', 'telephone', 'ville',
        // Position
        'etablissement', 'poste_souhaite', 'poste_autre',
        // Contract types
        'contrat_cdi', 'contrat_cdd', 'contrat_temps_plein', 'contrat_temps_partiel',
        // CV
        'cv_path', 'cv_original_name', 'cv_mime_type', 'cv_size',
        // Cover letter
        'lettre_path', 'lettre_original_name', 'lettre_mime_type', 'lettre_size',
        // Diplomas
        'diplomes',
        // Availability
        'disponibilite', 'disponibilite_autre',
        // Message
        'message',
        // Admin
        'statut', 'priorite', 'note_recruteur', 'date_premier_contact', 'traite_par',
        // Tracking
        'source', 'ip_address', 'user_agent', 'metadata',
    ];

    protected $casts = [
        'diplomes' => 'array',
        'metadata' => 'array',
        'date_premier_contact' => 'datetime',
        // Booleans
        'contrat_cdi' => 'boolean',
        'contrat_cdd' => 'boolean',
        'contrat_temps_plein' => 'boolean',
        'contrat_temps_partiel' => 'boolean',
    ];

    // 🔗 Relations
    public function recruiter()
    {
        return $this->belongsTo(\App\Models\User::class, 'traite_par');
    }

    // ✅ Scopes utiles pour le backoffice RH
    public function scopeParPoste($query, $poste)
    {
        return $query->where('poste_souhaite', $poste);
    }

    public function scopeNouvelles($query)
    {
        return $query->where('statut', 'nouveau');
    }

    public function scopeAvecCV($query)
    {
        return $query->whereNotNull('cv_path');
    }

    public function scopeDisponibilite($query, $dispo)
    {
        return $query->where('disponibilite', $dispo);
    }

    // 📄 Helpers pour les fichiers
    public function getCvUrlAttribute()
    {
        return $this->cv_path ? Storage::url($this->cv_path) : null;
    }

    public function getLettreUrlAttribute()
    {
        return $this->lettre_path ? Storage::url($this->lettre_path) : null;
    }

    public function getDiplomesUrlsAttribute()
    {
        return collect($this->diplomes ?? [])
            ->map(fn($doc) => [
                ...$doc,
                'url' => Storage::url($doc['path'])
            ])
            ->toArray();
    }

    // 🗑️ Suppression propre des fichiers lors de la suppression de la candidature
    protected static function booted()
    {
        static::deleting(function ($application) {
            if ($application->cv_path && Storage::exists($application->cv_path)) {
                Storage::delete($application->cv_path);
            }
            if ($application->lettre_path && Storage::exists($application->lettre_path)) {
                Storage::delete($application->lettre_path);
            }
            // Diplômes
            foreach ($application->diplomes ?? [] as $doc) {
                if (isset($doc['path']) && Storage::exists($doc['path'])) {
                    Storage::delete($doc['path']);
                }
            }
        });
    }

    // 📧 Format pour emails
    public function getNomCompletAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getContratsSelectionnesAttribute()
    {
        $contrats = [];
        if ($this->contrat_cdi) $contrats[] = 'CDI';
        if ($this->contrat_cdd) $contrats[] = 'CDD';
        if ($this->contrat_temps_plein) $contrats[] = 'Temps plein';
        if ($this->contrat_temps_partiel) $contrats[] = 'Temps partiel';
        return $contrats;
    }
}