<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Categorie;
use App\Models\Publication;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
        $donnees = $request->validated();

        if ($request->hasFile('image')) {
            $donnees['image'] = $request->file('image')->store('publications', 'public');
        }

        if ($request->hasFile('fichier_media')) {
            $fichier = $request->file('fichier_media');
            $donnees['fichier_media'] = $fichier->store('medias', 'public');
            $donnees['type_media'] = str_starts_with($fichier->getMimeType(), 'audio')
                ? 'audio'
                : 'video';
        }

        $donnees['user_id'] = auth()->id();

        $publication = Publication::create($donnees);

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
        Gate::authorize('update', $contenu);

        $categories = Categorie::all();
        return view('contenus.edit', ['publication' => $contenu, 'categories' => $categories]);
    }

    public function update(UpdatePublicationRequest $request, Publication $contenu)
    {
        Gate::authorize('update', $contenu);

        $donnees = $request->validated();

        // Nouvelle image : on supprime l'ancienne avant d'enregistrer la nouvelle
        if ($request->hasFile('image')) {
            if ($contenu->image) {
                Storage::disk('public')->delete($contenu->image);
            }
            $donnees['image'] = $request->file('image')->store('publications', 'public');
        }

        // Nouveau média audio/vidéo : même principe
        if ($request->hasFile('fichier_media')) {
            if ($contenu->fichier_media) {
                Storage::disk('public')->delete($contenu->fichier_media);
            }
            $fichier = $request->file('fichier_media');
            $donnees['fichier_media'] = $fichier->store('medias', 'public');
            $donnees['type_media'] = str_starts_with($fichier->getMimeType(), 'audio')
                ? 'audio'
                : 'video';
        }

        $contenu->update($donnees);

        return redirect()
            ->route('contenus.show', $contenu)
            ->with('succes', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $contenu)
    {
        Gate::authorize('delete', $contenu);

        $contenu->delete();

        return redirect()
            ->route('contenus.index')
            ->with('succes', 'Publication supprimée avec succès.');
    }
}