<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // 🔹 Informations du contact
            $table->string('nom');
            $table->string('email');
            $table->string('telephone')->nullable();

            // 🔹 Détails du message
            $table->string('sujet'); // admission, information, visite, autre
            $table->text('message');

            // 🔹 Administration & Tracking
            $table->enum('statut', [
                'nouveau',
                'lu',
                'en_cours',
                'repondu',
                'archive'
            ])->default('nouveau');

            $table->enum('priorite', [
                'faible',
                'normale',
                'haute'
            ])->default('normale');

            $table->text('reponse_admin')->nullable();
            $table->timestamp('date_reponse')->nullable();
            $table->unsignedBigInteger('traite_par')->nullable();

            // 🔹 Métadonnées
            $table->string('source')->nullable(); // site, popup, footer
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // 🔗 Foreign Key (si table users existe)
            $table->foreign('traite_par')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // 🔍 Index pour les recherches fréquentes
            $table->index('email');
            $table->index('statut');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};