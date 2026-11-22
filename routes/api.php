<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicationController;
use Illuminate\Support\Facades\Route;

// Obtention d'un jeton
Route::post('/connexion', [AuthController::class, 'connecter']);

// Lecture publique des publications
Route::apiResource('publications', PublicationController::class)
    ->only(['index', 'show']);

// Écriture : jeton Sanctum obligatoire
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('publications', PublicationController::class)
        ->only(['store', 'update', 'destroy']);
});
