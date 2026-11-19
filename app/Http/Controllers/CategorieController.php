<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Support\Facades\Gate;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('publications')->paginate(5);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('gerer-categories');

        return view('categories.create');
    }

    public function store(StoreCategorieRequest $request)
    {
        Gate::authorize('gerer-categories');

        Categorie::create($request->validated());
        return redirect()
            ->route('categories.index')
            ->with('succes', 'Catégorie créée avec succès.');
    }

    public function show(Categorie $categorie)
    {
        // Pas nécessaire cette semaine
    }

    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        $categorie->update($request->validated());
        return redirect()
            ->route('categories.index')
            ->with('succes', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        Gate::authorize('gerer-categories');

        $categorie->delete();
        return redirect()
            ->route('categories.index')
            ->with('succes', 'Catégorie supprimée avec succès.');
    }
}