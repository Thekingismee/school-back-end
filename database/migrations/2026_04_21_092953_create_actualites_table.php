<?php
// database/migrations/xxxx_xx_xx_create_actualites_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actualites', function (Blueprint $table) {
            $table->id();
            
            // Chemin de l'image principale (ex: "/actu1.jpg")
            $table->string('image')->nullable();
                        
            // Date de publication (stockée en DATE, formatée en frontend)
            $table->date('date_publication');
            
            // Catégorie : Sport, Commémoration, Vie Scolaire, etc.
            $table->string('categorie', 50);
            
            // Titre de l'actualité
            $table->string('titre', 255);
            
            // Description complète (contenu riche)
            $table->text('description');
            
            // Slug pour les URLs SEO-friendly (optionnel mais recommandé)
            $table->string('slug')->unique()->nullable();
            
            // Statut de publication (brouillon/publié)
            $table->enum('statut', ['brouillon', 'publie'])->default('brouillon');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actualites');
    }
};