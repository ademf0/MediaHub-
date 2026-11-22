<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequetesTest extends TestCase
{
    use RefreshDatabase;

    // Exécute une action et renvoie le nombre de requêtes SQL qu'elle a provoquées
    private function compter(callable $action): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $action();

        $nombre = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $nombre;
    }

    public function test_mesure_des_requetes_de_la_liste_des_publications(): void
    {
        Publication::factory()->count(9)->create();

        // AVANT : accès aux relations sans chargement anticipé (le cas N+1)
        $sansChargement = $this->compter(function () {
            foreach (Publication::latest()->take(9)->get() as $publication) {
                $categorie = $publication->categorie->nom;
                $auteur = $publication->auteur->name;
            }
        });

        // APRÈS : chargement anticipé avec with() et withCount()
        $avecChargement = $this->compter(function () {
            $publications = Publication::with(['categorie', 'auteur'])
                ->withCount('commentaires')
                ->latest()
                ->take(9)
                ->get();

            foreach ($publications as $publication) {
                $categorie = $publication->categorie->nom;
                $auteur = $publication->auteur->name;
            }
        });

        // LA PAGE RÉELLE /contenus
        $pageReelle = $this->compter(fn () => $this->get('/contenus')->assertOk());

        echo PHP_EOL;
        echo "Sans chargement anticipé (9 publications) : {$sansChargement} requêtes" . PHP_EOL;
        echo "Avec with() et withCount()                 : {$avecChargement} requêtes" . PHP_EOL;
        echo "Page réelle /contenus                      : {$pageReelle} requêtes" . PHP_EOL;

        $this->assertLessThan($sansChargement, $avecChargement);
        $this->assertLessThanOrEqual(8, $pageReelle);
    }
}