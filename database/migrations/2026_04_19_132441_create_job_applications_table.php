<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // 👤 Informations personnelles
            $table->string('nom');
            $table->string('prenom');
            $table->string('email');
            $table->string('telephone');
            $table->string('ville');

            // 🏫 Établissement & Poste
            $table->string('etablissement')->default("l'Atome-lissasfa");
            $table->string('poste_souhaite'); // enseignant-maternelle, direction, etc.
            $table->string('poste_autre')->nullable(); // Si poste = "autre"

            // 📋 Type de contrat (stockés en colonnes booléennes pour faciliter les recherches)
            $table->boolean('contrat_cdi')->default(false);
            $table->boolean('contrat_cdd')->default(false);
            $table->boolean('contrat_temps_plein')->default(false);
            $table->boolean('contrat_temps_partiel')->default(false);

            // 📁 Pièces jointes - Stockage sécurisé
            $table->string('cv_path');                 // Chemin du fichier CV
            $table->string('cv_original_name');        // Nom original du fichier
            $table->string('cv_mime_type');            // application/pdf, etc.
            $table->unsignedInteger('cv_size');        // Taille en octets

            $table->string('lettre_path')->nullable();
            $table->string('lettre_original_name')->nullable();
            $table->string('lettre_mime_type')->nullable();
            $table->unsignedInteger('lettre_size')->nullable();

            $table->json('diplomes')->nullable();      // [{name, path, size, mime}, ...]

            // 🕒 Disponibilité
            $table->enum('disponibilite', [
                'immediate',
                '1mois',
                'rentree',
                'autre'
            ]);
            $table->string('disponibilite_autre')->nullable();

            // 💬 Message
            $table->text('message')->nullable();

            // 🔐 Administration & Suivi
            $table->enum('statut', [
                'nouveau',
                'en_cours',
                'contacte',
                'entretien',
                'accepte',
                'refuse',
                'archive'
            ])->default('nouveau');

            $table->enum('priorite', [
                'faible', 'normale', 'haute'
            ])->default('normale');

            $table->text('note_recruteur')->nullable();
            $table->timestamp('date_premier_contact')->nullable();
            $table->unsignedBigInteger('traite_par')->nullable();

            // 🔍 Tracking
            $table->string('source')->nullable(); // site, linkedin, indeed, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Données supplémentaires

            $table->softDeletes();
            $table->timestamps();

            // 🔗 Foreign Keys
            $table->foreign('traite_par')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // 🔍 Index pour performances
            $table->index('email');
            $table->index('poste_souhaite');
            $table->index('statut');
            $table->index('created_at');
            $table->index(['etablissement', 'poste_souhaite']); // Recherche par poste/établissement
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};