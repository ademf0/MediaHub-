<?php

namespace Tests\Feature\Api;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicationApiTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Lecture publique ----------

    public function test_liste_des_publications_via_api(): void
    {
        Publication::factory()->count(5)->create();

        $reponse = $this->getJson('/api/publications');

        $reponse->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_detail_dune_publication_via_api(): void
    {
        $publication = Publication::factory()->create();

        $reponse = $this->getJson("/api/publications/{$publication->id}");

        $reponse->assertStatus(200)
            ->assertJsonPath('data.id', $publication->id)
            ->assertJsonPath('data.titre', $publication->titre);
    }

    public function test_la_reponse_nexpose_pas_de_donnees_internes(): void
    {
        $publication = Publication::factory()->create();

        $reponse = $this->getJson("/api/publications/{$publication->id}");

        $reponse->assertJsonMissingPath('data.user_id')
            ->assertJsonMissingPath('data.document')
            ->assertJsonMissingPath('data.deleted_at');
    }

    public function test_une_publication_inexistante_renvoie_404(): void
    {
        $this->getJson('/api/publications/99999')->assertStatus(404);
    }

    public function test_filtre_par_categorie(): void
    {
        $audio = Categorie::factory()->create(['nom' => 'Audio', 'slug' => 'audio']);
        $video = Categorie::factory()->create(['nom' => 'Video', 'slug' => 'video']);
        Publication::factory()->count(2)->create(['categorie_id' => $audio->id]);
        Publication::factory()->count(3)->create(['categorie_id' => $video->id]);

        $reponse = $this->getJson("/api/publications?categorie_id={$audio->id}");

        $reponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_liste_des_categories_via_api(): void
    {
        Categorie::factory()->count(3)->create();

        $reponse = $this->getJson('/api/categories');

        $reponse->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    // ---------- Authentification par jeton ----------

    public function test_connexion_api_renvoie_un_jeton(): void
    {
        User::factory()->create([
            'email' => 'api@exemple.com',
            'password' => bcrypt('bonmotdepasse'),
        ]);

        $reponse = $this->postJson('/api/connexion', [
            'email' => 'api@exemple.com',
            'password' => 'bonmotdepasse',
        ]);

        $reponse->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_connexion_api_avec_mauvais_mot_de_passe(): void
    {
        User::factory()->create([
            'email' => 'api@exemple.com',
            'password' => bcrypt('bonmotdepasse'),
        ]);

        $reponse = $this->postJson('/api/connexion', [
            'email' => 'api@exemple.com',
            'password' => 'faux',
        ]);

        $reponse->assertStatus(401);
    }

    // ---------- Écriture protégée ----------

    public function test_creation_sans_jeton_est_refusee(): void
    {
        $reponse = $this->postJson('/api/publications', []);

        $reponse->assertStatus(401);
    }

    public function test_suppression_sans_jeton_est_refusee(): void
    {
        $publication = Publication::factory()->create();

        $this->deleteJson("/api/publications/{$publication->id}")->assertStatus(401);
        $this->assertNotSoftDeleted('publications', ['id' => $publication->id]);
    }

    public function test_creation_avec_jeton_renvoie_201(): void
    {
        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create();
        Sanctum::actingAs($utilisateur);

        $reponse = $this->postJson('/api/publications', [
            'titre' => 'Publication creee par API',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
        ]);

        $reponse->assertStatus(201)
            ->assertJsonPath('data.titre', 'Publication creee par API');
        $this->assertDatabaseHas('publications', [
            'titre' => 'Publication creee par API',
            'user_id' => $utilisateur->id,
        ]);
    }

    public function test_creation_avec_donnees_invalides_renvoie_422(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $reponse = $this->postJson('/api/publications', ['titre' => 'court']);

        $reponse->assertStatus(422)
            ->assertJsonValidationErrors(['titre', 'contenu', 'categorie_id']);
    }

    public function test_modifier_la_publication_dun_autre_renvoie_403(): void
    {
        $auteur = User::factory()->create();
        $tiers = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);
        Sanctum::actingAs($tiers);

        $reponse = $this->putJson("/api/publications/{$publication->id}", [
            'titre' => 'Titre modifie par un tiers',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $publication->categorie_id,
        ]);

        $reponse->assertStatus(403);
    }

    public function test_un_auteur_peut_supprimer_sa_publication_via_api(): void
    {
        $auteur = User::factory()->create();
        $publication = Publication::factory()->create(['user_id' => $auteur->id]);
        Sanctum::actingAs($auteur);

        $reponse = $this->deleteJson("/api/publications/{$publication->id}");

        $reponse->assertStatus(204);
        $this->assertSoftDeleted('publications', ['id' => $publication->id]);
    }
}