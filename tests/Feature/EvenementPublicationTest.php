<?php

namespace Tests\Feature;

use App\Events\PublicationCreee;
use App\Listeners\JournaliserPublication;
use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class EvenementPublicationTest extends TestCase
{
    use RefreshDatabase;

    private function creerUnePublication(): void
    {
        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $this->actingAs($utilisateur)->post('/contenus', [
            'titre' => 'Une publication de test',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
        ]);
    }

    public function test_la_creation_declenche_levenement(): void
    {
        Event::fake([PublicationCreee::class]);

        $this->creerUnePublication();

        Event::assertDispatched(
            PublicationCreee::class,
            fn ($evenement) => $evenement->publication->titre === 'Une publication de test'
        );
    }

    public function test_une_creation_refusee_ne_declenche_pas_levenement(): void
    {
        Event::fake([PublicationCreee::class]);

        $this->actingAs(User::factory()->create())->post('/contenus', ['titre' => 'court']);

        Event::assertNotDispatched(PublicationCreee::class);
    }

    public function test_lecouteur_journalise_la_publication(): void
    {
        Log::spy();
        $publication = Publication::factory()->create();

        (new JournaliserPublication())->handle(new PublicationCreee($publication));

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Publication créée', \Mockery::on(fn ($contexte) => $contexte['id'] === $publication->id));
    }

    public function test_la_creation_journalise_bien_de_bout_en_bout(): void
    {
        Log::spy();

        $this->creerUnePublication();

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Publication créée', \Mockery::type('array'));
    }
}