<?php

namespace App\Http\Controllers;

use App\Models\Publication;

class PageController extends Controller
{
    public function accueil()
    {
        $dernieres = Publication::with(['categorie', 'auteur'])->latest()->take(5)->get();
        return view('accueil', compact('dernieres'));
    }

    public function aPropos()
    {
        return view('a-propos');
    }
}