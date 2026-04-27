<?php

use App\Http\Controllers\Api\InscriptionController;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Route::post('/inscriptions', [InscriptionController::class, 'store']);




Route::get('/test-email', function () {
    
    // Données de test
    $fakeData = [
        'nom' => 'Jean Dupont (TEST)',
        'email' => 'test@example.com',
        'telephone' => '+212 6 00 00 00 00',
        'sujet' => 'information',
        'message' => "Ceci est un message de test pour vérifier la configuration d'envoi d'email.\n\nSi vous lisez ceci, tout fonctionne ! 🎉",
    ];

    try {
        // Envoi du mail de test
        Mail::to(config('mail.admin_email', 'stagiaireatome@gmail.com'))
            ->send(new ContactFormMail($fakeData));

        return response()->json([
            'success' => true,
            'message' => '✅ Email de test envoyé avec succès !',
            'to' => config('mail.admin_email', 'stagiaireatome@gmail.com'),
            'sent_at' => now()->toDateTimeString(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '❌ Échec de l\'envoi',
            'error' => $e->getMessage(),
            'log' => 'Vérifiez storage/logs/laravel.log',
        ], 500);
    }
});


require __DIR__.'/auth.php';
