<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ContenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'accueil']);
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

Route::get('/contenus', [ContenuController::class, 'index']);
Route::get('/contenus/{id}', [ContenuController::class, 'show'])->name('contenus.show');