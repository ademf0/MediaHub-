<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CacheCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_deuxieme_appel_ne_fait_aucune_requete(): void
    {
        Categorie::factory()->count(3)->create();

        Categorie::pourFormulaires(); // remplit le cache

        DB::flushQueryLog();
        DB::enableQueryLog();
        $liste = Categorie::pourFormulaires();
        $requetes = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertCount(3, $liste);
        $this->assertSame(0, $requetes);
    }

    public function test_le_cache_est_invalide_apres_une_creation(): void
    {
        Categorie::factory()->create(['nom' => 'Video', 'slug' => 'video']);
        $this->assertCount(1, Categorie::pourFormulaires());

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post('/categories', ['nom' => 'Podcast', 'slug' => 'podcast']);

        $this->assertCount(2, Categorie::pourFormulaires());
    }

    public function test_le_cache_est_invalide_apres_une_suppression(): void
    {
        $categorie = Categorie::factory()->create();
        Categorie::factory()->create();
        $this->assertCount(2, Categorie::pourFormulaires());

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->delete("/categories/{$categorie->id}");

        $this->assertCount(1, Categorie::pourFormulaires());
    }

    public function test_le_formulaire_de_creation_affiche_les_categories(): void
    {
        Categorie::factory()->create(['nom' => 'Podcast', 'slug' => 'podcast']);
        $utilisateur = User::factory()->create();

        $reponse = $this->actingAs($utilisateur)->get('/contenus/create');

        $reponse->assertOk();
        $reponse->assertSeeText('Podcast');
    }
}