<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // 🔹 Informations du visiteur
            $table->string('nom');
            $table->string('email');
            $table->string('telephone');
            $table->string('invites')->nullable(); // Emails séparés par des virgules
            $table->text('message')->nullable();

            // 🔹 Détails du rendez-vous
            $table->date('date_rdv');           // ex: 2026-03-15
            $table->time('heure_rdv');          // ex: 14:30:00
            $table->integer('duree_minutes')->default(15);
            $table->string('lieu')->default('Lissasfa'); // Pour multi-sites futur

            // 🔹 Statut & Gestion
            $table->enum('statut', [
                'pending',      // En attente de confirmation
                'confirmed',    // Confirmé
                'cancelled',    // Annulé par le visiteur
                'rejected',     // Refusé par l'admin
                'completed',    // Rendez-vous passé
            ])->default('pending');

            $table->enum('priorite', [
                'faible', 'normale', 'haute'
            ])->default('normale');

            $table->text('note_admin')->nullable();
            $table->timestamp('confirme_le')->nullable();
            $table->unsignedBigInteger('confirme_par')->nullable();

            // 🔹 Tracking & Métadonnées
            $table->string('source')->nullable(); // site, popup, mobile
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Données supplémentaires (navigateur, etc.)

            $table->softDeletes();
            $table->timestamps();

            // 🔗 Foreign Keys
            $table->foreign('confirme_par')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // 🔍 Index pour performances
            $table->index(['date_rdv', 'heure_rdv']);
            $table->index('email');
            $table->index('statut');
            $table->unique(['date_rdv', 'heure_rdv', 'lieu'], 'unique_slot_lieu'); // Évite les doublons de créneau
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};