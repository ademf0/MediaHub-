<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    // Données valides pour une modification (le titre fait plus de 10 caractères)
    private function donneesValides(Publication $publication): array
    {
        return [
            'titre' => 'Titre modifié par le test',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $publication->categorie_id,
        ];
    }

    // ---------- Lecture publique ----------

    public function test_la_liste_des_publications_est_publique(): void
    {
        Publication::factory()->count(3)->create();

        $reponse = $this->get('/contenus');

        $reponse->assertStatus(200);
        $reponse->assertSeeText('Contenus multimédias');
    }

    public function test_la_page_dune_publication_est_publique(): void
    {
        $publication = Publication::factory()->create();

        $reponse = $this->get("/contenus/{$publication->id}");

        $reponse->assertStatus(200);
        $reponse->assertSeeText($publication->titre);
    }

    // ---------- Création ----------

    public function test_un_visiteur_ne_peut_pas_creer_de_publication(): void
    {
        $this->get('/contenus/create')->assertRedirect('/connexion');
        $this->post('/contenus', [])->assertRedirect('/connexion');
    }

    public function test_un_utilisateur_connecte_peut_creer_une_publication(): void
    {
        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $reponse = $this->actingAs($utilisateur)->post('/contenus', [
            'titre' => 'Une publication de test',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
        ]);

        $this->assertDatabaseHas('publications', [
            'titre' => 'Une publication de test',
            'user_id' => $utilisateur->id,
        ]);

        $publication = Publication::firstOrFail();
        $reponse->assertRedirect("/contenus/{$publication->id}");
    }

    public function test_lauteur_vient_de_la_session_et_non_du_formulaire(): void
    {
        $utilisateur = User::factory()->create();
        $autre = User::factory()->create();
        $categorie = Categorie::factory()->create();

        // Tentative de se faire passer pour un autre auteur
        $this->actingAs($utilisateur)->post('/contenus', [
            'titre' => 'Une publication de test',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
            'user_id' => $autre->id,
        ]);

        $this->assertDatabaseHas('publications', [
            'titre' => 'Une publication de test',
            'user_id' => $utilisateur->id,
        ]);
    }

    public function test_un_titre_trop_court_est_refuse(): void
    {
        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        $reponse = $this->actingAs($utilisateur)->post('/contenus', [
            'titre' => 'Court',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
        ]);

        $reponse->assertSessionHasErrors('titre');
        $this->assertDatabaseCount('publications', 0);
    }

    // ---------- Modification et autorisations ----------

    public function test_un_auteur_peut_modifier_sa_propre_publication(): void
    {
        $auteur = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);

        $reponse = $this->actingAs($auteur)
            ->put("/contenus/{$publication->id}", $this->donneesValides($publication));

        $reponse->assertRedirect("/contenus/{$publication->id}");
        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'titre' => 'Titre modifié par le test',
        ]);
    }

    public function test_un_auteur_ne_peut_pas_modifier_une_autre_publication(): void
    {
        $auteur = User::factory()->create();
        $tiers = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);

        $reponse = $this->actingAs($tiers)
            ->put("/contenus/{$publication->id}", $this->donneesValides($publication));

        $reponse->assertForbidden();
        $this->assertDatabaseMissing('publications', ['titre' => 'Titre modifié par le test']);
    }

    public function test_un_admin_peut_modifier_nimporte_quelle_publication(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $publication = Publication::factory()->create();

        $reponse = $this->actingAs($admin)
            ->put("/contenus/{$publication->id}", $this->donneesValides($publication));

        $reponse->assertRedirect("/contenus/{$publication->id}");
        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'titre' => 'Titre modifié par le test',
        ]);
    }

    // ---------- Suppression ----------

    public function test_un_auteur_peut_supprimer_sa_propre_publication(): void
    {
        $auteur = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);

        $reponse = $this->actingAs($auteur)->delete("/contenus/{$publication->id}");

        $reponse->assertRedirect('/contenus');
        $this->assertSoftDeleted('publications', ['id' => $publication->id]);
    }

    public function test_un_auteur_ne_peut_pas_supprimer_la_publication_dun_autre(): void
    {
        $auteur = User::factory()->create();
        $tiers = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);

        $reponse = $this->actingAs($tiers)->delete("/contenus/{$publication->id}");

        $reponse->assertForbidden();
        $this->assertNotSoftDeleted('publications', ['id' => $publication->id]);
    }
}
