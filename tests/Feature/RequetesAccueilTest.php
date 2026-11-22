<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequetesAccueilTest extends TestCase
{
    use RefreshDatabase;

    public function test_nombre_de_requetes_de_la_page_daccueil(): void
    {
        Publication::factory()->count(5)->create();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->get('/')->assertOk();

        $requetes = count(DB::getQueryLog());
        DB::disableQueryLog();

        echo PHP_EOL . "Page d'accueil : {$requetes} requêtes" . PHP_EOL;

        // Objectif : 1 requête pour les publications + 1 pour les catégories + 1 pour les auteurs
        $this->assertLessThanOrEqual(3, $requetes);
    }
}