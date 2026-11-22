<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/connexion : renvoie un jeton si les identifiants sont corrects
    public function connecter(Request $request)
    {
        $identifiants = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // On vérifie le mot de passe à la main : une route API n'a pas de session,
        // donc Auth::attempt() (qui s'appuie sur la session) n'est pas adapté ici.
        $utilisateur = User::where('email', $identifiants['email'])->first();

        if (! $utilisateur || ! Hash::check($identifiants['password'], $utilisateur->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        $jeton = $utilisateur->createToken('api')->plainTextToken;

        return response()->json(['token' => $jeton]);
    }
}