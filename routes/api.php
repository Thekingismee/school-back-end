<?php

use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\InscriptionController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    // 🔓 Routes publiques (à protéger avec auth:sanctum si nécessaire)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});


Route::post('/inscriptions', [InscriptionController::class, 'store']);

Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {
    
    // 📋 Liste des inscriptions avec filtres
    Route::get('/inscriptions', [InscriptionController::class, 'index']);
    
    // ✏️ Mise à jour du statut
    Route::patch('/inscriptions/{inscription}/status', [InscriptionController::class, 'updateStatus']);
    
    // 🗑️ Suppression (soft delete)
    Route::delete('/inscriptions/{inscription}', [InscriptionController::class, 'destroy']);
    
});





Route::post('/contact-messages', [ContactController::class, 'store']);

Route::post('/contact-messages-plus', [ContactMessageController::class, 'store']);
// 🔐 Routes protégées pour l'admin (recommandé)
Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {
    
    // 📋 Liste des messages avec filtres
    Route::get('/messages', [ContactMessageController::class, 'index']);
    
    // ✏️ Mise à jour (statut, réponse admin, priorité)
    Route::patch('/messages/{contactMessage}', [ContactMessageController::class, 'update']);
    
    // 🗑️ Suppression (soft delete)
    Route::delete('/messages/{contactMessage}', [ContactMessageController::class, 'destroy']);
    
});

// 🔓 Route publique pour envoyer un message





Route::post('/appointments', [AppointmentController::class, 'store']);
Route::post('/appointments/check-availability', [AppointmentController::class, 'checkAvailability']);

Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {
    
    // 📋 Liste des rendez-vous avec filtres
    Route::get('/rendezvous', [AppointmentController::class, 'index']);
    
    // ✏️ Mise à jour (statut, priorité, note)
    Route::patch('/rendezvous/{appointment}', [AppointmentController::class, 'update']);
    
    // 🗑️ Suppression (soft delete)
    Route::delete('/rendezvous/{appointment}', [AppointmentController::class, 'destroy']);
    
    // 🔍 Vérifier disponibilité (optionnel pour admin)
    Route::post('/rendezvous/check-availability', [AppointmentController::class, 'checkAvailability']);
    
});


Route::post('/job-applications', [JobApplicationController::class, 'store']);



Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {
    
    // 📋 Liste des candidatures avec filtres
    Route::get('/candidatures', [JobApplicationController::class, 'index']);
    
    // ✏️ Mise à jour (statut, priorité, note recruteur)
    Route::patch('/candidatures/{jobApplication}', [JobApplicationController::class, 'update']);
    
    // 🗑️ Suppression (soft delete)
    Route::delete('/candidatures/{jobApplication}', [JobApplicationController::class, 'destroy']);
    
    // 📥 Téléchargement des fichiers (CV, lettre, diplômes)
    Route::get('/candidatures/{jobApplication}/download/{type}/{diplomeIndex?}', 
        [JobApplicationController::class, 'downloadFile'])
        ->where('type', 'cv|lettre|diplome')
        ->where('diplomeIndex', '[0-9]+');
    
});




Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {
    
    // 📋 Liste des actualités avec filtres
    Route::get('/actualites', [ActualiteController::class, 'index']);
    
    // ➕ Création d'une nouvelle actualité
    Route::post('/actualites', [ActualiteController::class, 'store']);
    
    // 👁️ Affichage d'une actualité spécifique
    Route::get('/actualites/{slug}', [ActualiteController::class, 'show']);
    
    // ✏️ Mise à jour (optionnel - à ajouter si besoin)
    // Route::put('/actualites/{actualite}', [ActualiteController::class, 'update']);
    
    // 🗑️ Suppression (optionnel - à ajouter si besoin)
    // Route::delete('/actualites/{actualite}', [ActualiteController::class, 'destroy']);
    
});

Route::get("/actualites/recent",[ActualiteController::class, 'getRecent']);

// 🔓 Routes publiques (pour affichage frontend)
Route::get('/actualites', function(\Illuminate\Http\Request $request) {
    // Retourner uniquement les actualités publiées pour le public
    return app(\App\Http\Controllers\ActualiteController::class)
        ->index($request->merge(['statut' => 'publie']));
});

// 👁️ Affichage détaillé d'une actualité publique
Route::get('/actualites/{slug}', function($slug) {
    // Retourner uniquement les actualités publiées
    $actualite = \App\Models\Actualite::where('slug', $slug)
        ->where('statut', 'publie')
        ->first();

    if (!$actualite) {
        return response()->json([
            'message' => 'Actualité non trouvée'
        ], 404);
    }

    return app(\App\Http\Controllers\ActualiteController::class)->show($slug);
});





