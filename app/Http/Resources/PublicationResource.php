<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PublicationResource extends JsonResource
{
    /**
     * Structure de la réponse JSON : on n'expose que ce qui est utile.
     * Le document PDF privé et les identifiants internes ne sont volontairement pas inclus.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'categorie' => $this->categorie?->nom,
            'auteur' => $this->auteur?->name,
            'image' => $this->image ? Storage::url($this->image) : null,
            'type_media' => $this->type_media,
            'media' => $this->fichier_media ? Storage::url($this->fichier_media) : null,
            'publie_le' => $this->created_at?->toDateString(),
        ];
    }
}