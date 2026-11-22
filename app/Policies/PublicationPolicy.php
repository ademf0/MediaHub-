<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    // L'auteur de la publication ou un administrateur peut la modifier
    public function update(User $utilisateur, Publication $publication): bool
    {
        return $utilisateur->id === (int) $publication->user_id
            || $utilisateur->role === 'admin';
    }

    // Mêmes règles pour la suppression
    public function delete(User $utilisateur, Publication $publication): bool
    {
        return $this->update($utilisateur, $publication);
    }
        // Le document n'est visible que par l'auteur ou un administrateur
    public function voirDocument(User $utilisateur, Publication $publication): bool
    {
        return $this->update($utilisateur, $publication);
    }
}
