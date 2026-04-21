<?php

use App\Http\Controllers\Api\InscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Route::post('/inscriptions', [InscriptionController::class, 'store']);

require __DIR__.'/auth.php';
