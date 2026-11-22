<?php

namespace App\Listeners;

use App\Events\PublicationCreee;
use Illuminate\Support\Facades\Log;


class JournaliserPublication
{
    public function handle(PublicationCreee $evenement): void
    {
        Log::info('Publication créée', [
            'id' => $evenement->publication->id,
            'titre' => $evenement->publication->titre,
            'user_id' => $evenement->publication->user_id,
        ]);
    }
}