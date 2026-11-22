<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Http\Resources\PublicationResource;
use App\Models\Publication;
use Illuminate\Support\Facades\Gate;

class PublicationController extends Controller
{
    // GET /api/publications : liste paginée (accès public)
    public function index()
    {
        $publications = Publication::with(['categorie', 'auteur'])
            ->latest()
            ->paginate(10);

        return PublicationResource::collection($publications);
    }

    // GET /api/publications/{publication} : détail (accès public)
    public function show(Publication $publication)
    {
        return new PublicationResource(
            $publication->load(['categorie', 'auteur'])
        );
    }

    // POST /api/publications : création (jeton requis)
    public function store(StorePublicationRequest $request)
    {
        $publication = Publication::create([
            ...$request->safe()->only(['titre', 'contenu', 'categorie_id']),
            'user_id' => $request->user()->id,
        ]);

        return (new PublicationResource($publication->load(['categorie', 'auteur'])))
            ->response()
            ->setStatusCode(201);
    }

    // PUT/PATCH /api/publications/{publication} : modification (jeton requis, auteur ou admin)
    public function update(UpdatePublicationRequest $request, Publication $publication)
    {
        Gate::authorize('update', $publication);

        $publication->update($request->safe()->only(['titre', 'contenu', 'categorie_id']));

        return new PublicationResource($publication->load(['categorie', 'auteur']));
    }

    // DELETE /api/publications/{publication} : suppression (jeton requis, auteur ou admin)
    public function destroy(Publication $publication)
    {
        Gate::authorize('delete', $publication);

        $publication->delete();

        return response()->json(null, 204);
    }
}