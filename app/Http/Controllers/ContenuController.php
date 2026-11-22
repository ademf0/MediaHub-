<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Categorie;
use App\Models\Publication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Events\PublicationCreee;

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
        $categories = Categorie::pourFormulaires();
        return view('contenus.create', compact('categories'));
    }

    public function store(StorePublicationRequest $request)
    {
        $donnees = $request->validated();

        if ($request->hasFile('image')) {
            $donnees['image'] = $this->enregistrerImage($request->file('image'));
        }

        if ($request->hasFile('fichier_media')) {
            $fichier = $request->file('fichier_media');
            $donnees['fichier_media'] = $fichier->store('medias', 'public');
            $donnees['type_media'] = str_starts_with($fichier->getMimeType(), 'audio')
                ? 'audio'
                : 'video';
        }

        if ($request->hasFile('document')) {
            $donnees['document'] = $request->file('document')->store('documents', 'local');
        }

        $donnees['user_id'] = auth()->id();

        $publication = Publication::create($donnees);
        PublicationCreee::dispatch($publication);
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

        $categories = Categorie::pourFormulaires();
        return view('contenus.edit', ['publication' => $contenu, 'categories' => $categories]);
    }

    public function update(UpdatePublicationRequest $request, Publication $contenu)
    {
        Gate::authorize('update', $contenu);

        $donnees = $request->validated();

        // Nouvelle image : on supprime l'ancienne (et sa miniature) avant d'enregistrer la nouvelle
        if ($request->hasFile('image')) {
            if ($contenu->image) {
                Storage::disk('public')->delete($contenu->image);
                Storage::disk('public')->delete($contenu->cheminMiniature());
            }
            $donnees['image'] = $this->enregistrerImage($request->file('image'));
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

        // Nouveau document privé
        if ($request->hasFile('document')) {
            if ($contenu->document) {
                Storage::disk('local')->delete($contenu->document);
            }
            $donnees['document'] = $request->file('document')->store('documents', 'local');
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

    // Sert le document privé, uniquement à son auteur ou à un administrateur
    public function document(Publication $publication)
    {
        Gate::authorize('voirDocument', $publication);

        abort_unless(
            $publication->document && Storage::disk('local')->exists($publication->document),
            404
        );

        return Storage::disk('local')->response(
            $publication->document,
            'document-' . $publication->id . '.pdf'
        );
    }

    // Stocke l'image originale et génère une miniature de 400x300
    private function enregistrerImage(UploadedFile $fichier): string
    {
        $chemin = $fichier->store('publications', 'public');

        try {
            $manager = new ImageManager(new Driver());

            $miniature = $manager->read($fichier->getRealPath())
                ->cover(400, 300)
                ->toJpeg(80);

            Storage::disk('public')->put(
                'publications/miniatures/' . pathinfo($chemin, PATHINFO_FILENAME) . '.jpg',
                (string) $miniature
            );
        } catch (\Throwable $e) {
            // La miniature est facultative : on n'empêche pas la publication si elle échoue
            report($e);
        }

        return $chemin;
    }
}