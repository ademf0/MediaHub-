<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_liste_des_categories_est_publique(): void
    {
        Categorie::factory()->count(3)->create();

        $reponse = $this->get('/categories');

        $reponse->assertStatus(200);
    }

    public function test_creation_dune_categorie_par_un_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $reponse = $this->actingAs($admin)->post('/categories', [
            'nom' => 'Podcast',
            'slug' => 'podcast',
        ]);

        $reponse->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['slug' => 'podcast']);
    }

    public function test_un_auteur_ne_peut_pas_creer_de_categorie(): void
    {
        $auteur = User::factory()->create();

        $reponse = $this->actingAs($auteur)->post('/categories', [
            'nom' => 'Podcast',
            'slug' => 'podcast',
        ]);

        $reponse->assertForbidden();
        $this->assertDatabaseMissing('categories', ['slug' => 'podcast']);
    }

    public function test_un_visiteur_ne_peut_pas_creer_de_categorie(): void
    {
        $reponse = $this->post('/categories', [
            'nom' => 'Podcast',
            'slug' => 'podcast',
        ]);

        $reponse->assertForbidden();
        $this->assertDatabaseMissing('categories', ['slug' => 'podcast']);
    }

    public function test_un_slug_deja_utilise_est_refuse(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Categorie::factory()->create(['nom' => 'Video', 'slug' => 'video']);

        $reponse = $this->actingAs($admin)->post('/categories', [
            'nom' => 'Autre',
            'slug' => 'video',
        ]);

        $reponse->assertSessionHasErrors('slug');
        $this->assertDatabaseMissing('categories', ['nom' => 'Autre']);
    }

    public function test_modifier_une_categorie_en_gardant_son_slug_est_accepte(): void
    {
        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create(['nom' => 'Audio', 'slug' => 'audio']);

        $reponse = $this->actingAs($utilisateur)->put("/categories/{$categorie->id}", [
            'nom' => 'Audio modifie',
            'slug' => 'audio',
        ]);

        $reponse->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['id' => $categorie->id, 'nom' => 'Audio modifie']);
    }

    public function test_un_admin_peut_supprimer_une_categorie(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $categorie = Categorie::factory()->create();

        $reponse = $this->actingAs($admin)->delete("/categories/{$categorie->id}");

        $reponse->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $categorie->id]);
    }
}