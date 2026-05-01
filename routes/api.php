<?php

use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentMController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\InscriptionController;
use App\Http\Controllers\Api\InscriptionMController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobApplicationMController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/user', function (Request $request) {
//     return response()->json([
//         "success" => true,
        
//             "id" => 1,
//             "name" => "AtomBot User",
//             "email" => "user@groupe-scolaire-latome.ma",
//             "role" => "parent",
//             "email_verified_at" => now()->toIso8601String(),
//             "created_at" => now()->toIso8601String(),
//             "updated_at" => now()->toIso8601String()
//     ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
// });

Route::prefix('v1')->group(function () {
    // 🔓 Routes publiques (à protéger avec auth:sanctum si nécessaire)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::
// middleware('auth:sanctum')->
prefix('admin')->group(function () {

    // 📋 Liste des actualités avec filtres
    Route::get('/actualites', [ActualiteController::class, 'index']);
    Route::post('/actualites', [ActualiteController::class, 'store']);
    Route::get('/actualites/{slug}', [ActualiteController::class, 'show']);
    Route::delete('/actualites/{slug}', [ActualiteController::class, 'destroy'])->name('admin.actualites.destroy');

    
    // 📋 Liste des inscriptions avec filtres
    Route::get('/inscriptions', [InscriptionController::class, 'index']);
    Route::patch('/inscriptions/{inscription}/status', [InscriptionController::class, 'updateStatus']);
    Route::delete('/inscriptions/{inscription}', [InscriptionController::class, 'destroy']);
        
    // 📋 Liste des messages avec filtres
    Route::get('/messages', [ContactMessageController::class, 'index']);
    Route::patch('/messages/{contactMessage}', [ContactMessageController::class, 'update']);
    Route::delete('/messages/{contactMessage}', [ContactMessageController::class, 'destroy']);
        
    // 📋 Liste des rendez-vous avec filtres
    Route::get('/rendezvous', [AppointmentController::class, 'index']);
    Route::patch('/rendezvous/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/rendezvous/{appointment}', [AppointmentController::class, 'destroy']);
    Route::post('/rendezvous/check-availability', [AppointmentController::class, 'checkAvailability']);
    
    
    // 📋 Liste des candidatures avec filtres
    Route::get('/candidatures', [JobApplicationController::class, 'index']);
    Route::patch('/candidatures/{jobApplication}', [JobApplicationController::class, 'update']);
    Route::delete('/candidatures/{jobApplication}', [JobApplicationController::class, 'destroy']);
    // 📥 Téléchargement des fichiers (CV, lettre, diplômes)
    Route::get('/candidatures/{jobApplication}/download/{type}/{diplomeIndex?}', 
        [JobApplicationController::class, 'downloadFile'])
        ->where('type', 'cv|lettre|diplome')
        ->where('diplomeIndex', '[0-9]+');
        
});







Route::post('/inscriptions', [InscriptionMController::class, 'store']);
Route::post('/inscriptions-plus', [InscriptionController::class, 'store']);



Route::post('/contact-messages', [ContactController::class, 'store']);
Route::post('/contact-messages-plus', [ContactMessageController::class, 'store']);



Route::post('/appointments', [AppointmentMController::class, 'store']);
Route::post('/appointments-plus', [AppointmentController::class, 'store']);
Route::post('/appointments/check-availability', [AppointmentController::class, 'checkAvailability']);



Route::post('/job-applications', [JobApplicationController::class, 'store']);
Route::post('/job-applications-plus', [JobApplicationMController::class, 'store']);



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


Route::post('/subscribers', [SubscriberController::class, 'store']);
Route::post('/newsletter/send', [NewsletterController::class, 'send']);
Route::post('/upload-image', [UploadController::class, 'upload']);