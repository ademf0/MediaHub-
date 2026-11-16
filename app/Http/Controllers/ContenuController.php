<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ContenuController extends Controller
{
    public function index()
    {
        $publications = Publication::with(['categorie', 'auteur'])
            ->withCount('commentaires')
            ->latest()
            ->paginate(9);

        return view('contenus.index', ['publications' => $publications]);
    }

    public function create()
    {
        $categories = Categorie::all();
        return view('contenus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $publication = Publication::create([
            'titre' => $request->input('titre'),
            'contenu' => $request->input('contenu'),
            'categorie_id' => $request->input('categorie_id'),
            'user_id' => 1, // Provisoire en attendant l'authentification (semaine 8)
        ]);

        return redirect()
            ->route('contenus.show', $publication)
            ->with('succes', 'Publication créée avec succès.');
    }

    public function show(Publication $contenu)
    {
        $contenu->load(['categorie', 'auteur', 'commentaires.auteur', 'tags']);
        return view('contenus.show', ['publication' => $contenu]);
    }

    public function edit(Publication $contenu)
    {
        $categories = Categorie::all();
        return view('contenus.edit', ['publication' => $contenu, 'categories' => $categories]);
    }

    public function update(Request $request, Publication $contenu)
    {
        $contenu->update($request->only('titre', 'contenu', 'categorie_id'));

        return redirect()
            ->route('contenus.show', $contenu)
            ->with('succes', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $contenu)
    {
        $contenu->delete();

        return redirect()
            ->route('contenus.index')
            ->with('succes', 'Publication supprimée avec succès.');
    }
}