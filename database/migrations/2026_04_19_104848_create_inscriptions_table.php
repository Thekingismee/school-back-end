<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();

            // 🔹 Informations du parent
            $table->string('parent_nom');
            $table->string('telephone');
            $table->string('email')->nullable();

            // 🔹 Informations de l'élève
            $table->string('eleve_nom');
            $table->date('date_naissance');
            $table->enum('etablissement', ['maternelle', 'primaire', 'college', 'lycee']);
            $table->string('niveau');

            // 🔹 Message
            $table->text('message')->nullable();

            // =========================
            // 🔐 ADMINISTRATION
            // =========================

            // Statut de la demande
            $table->enum('statut', [
                'en_attente',
                'contacte',
                'accepte',
                'refuse'
            ])->default('en_attente');

            // Priorité
            $table->enum('priorite', [
                'faible',
                'normale',
                'haute'
            ])->default('normale');

            // Notes internes (admin)
            $table->text('note_admin')->nullable();

            // Date de contact
            $table->timestamp('date_contact')->nullable();

            // Agent / admin qui a traité
            $table->unsignedBigInteger('traite_par')->nullable();

            // Source (utile marketing)
            $table->string('source')->nullable(); // ex: facebook, site, whatsapp

            // IP utilisateur
            $table->ipAddress('ip_address')->nullable();

            // Soft delete (corbeille)
            $table->softDeletes();

            $table->timestamps();

            // Foreign key (optionnel si tu as users)
            $table->foreign('traite_par')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};