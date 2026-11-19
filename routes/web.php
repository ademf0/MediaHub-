<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\AuthController;

// Page d'accueil
Route::get('/', [PageController::class, 'accueil'])->name('accueil');

// Toutes les routes CRUD pour les contenus
Route::resource('contenus', ContenuController::class);

// Toutes les routes CRUD pour les catégories
Route::resource('categories', CategorieController::class)->parameters([
    'categories' => 'categorie'
]);

// Page à propos
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

// Routes d'administration
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', [AdminController::class, 'index'])->name('dashboard');
});

// Authentification : réservé aux visiteurs non connectés
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'create'])->name('inscription');
    Route::post('/inscription', [AuthController::class, 'store']);
    Route::get('/connexion', [AuthController::class, 'creerConnexion'])->name('connexion');
    Route::post('/connexion', [AuthController::class, 'connecter']);
});

// Déconnexion : réservé aux utilisateurs connectés
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'deconnecter'])->name('deconnexion');
});