<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Affiche le formulaire d'inscription
    public function create()
    {
        return view('auth.inscription');
    }

    // Crée le compte puis connecte l'utilisateur
    public function store(StoreUserRequest $request)
    {
        $utilisateur = User::create($request->validated());

        Auth::login($utilisateur);
        $request->session()->regenerate();

        return redirect()->route('accueil')
            ->with('succes', 'Bienvenue sur MediaHub !');
    }

    // Affiche le formulaire de connexion
    public function creerConnexion()
    {
        return view('auth.connexion');
    }

    // Vérifie les identifiants et ouvre la session
    public function connecter(Request $request)
    {
        $identifiants = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($identifiants)) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('accueil'));
    }

    // Ferme la session
    public function deconnecter(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}