<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicationResource;
use App\Models\Publication;

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
}