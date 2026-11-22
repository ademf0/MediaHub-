<?php

use App\Http\Controllers\Api\PublicationController;
use Illuminate\Support\Facades\Route;

// Lecture publique des publications
Route::apiResource('publications', PublicationController::class)
    ->only(['index', 'show']);
