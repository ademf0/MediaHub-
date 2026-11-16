<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Categorie;
use App\Models\Publication;

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

    public function store(StorePublicationRequest $request)
    {
        $publication = Publication::create(
            $request->validated() + ['user_id' => 1] // Provisoire jusqu'à la semaine 8
        );

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

    public function update(UpdatePublicationRequest $request, Publication $contenu)
    {
        $contenu->update($request->validated());

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