<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediasTest extends TestCase
{
    use RefreshDatabase;

    // Envoie une publication par ailleurs valide, avec un fichier dans le champ "image"
    private function envoyerAvecImage(UploadedFile $fichier)
    {
        Storage::fake('public');

        $utilisateur = User::factory()->create();
        $categorie = Categorie::factory()->create();

        return $this->actingAs($utilisateur)->post('/contenus', [
            'titre' => 'Une publication de test',
            'contenu' => str_repeat('a', 30),
            'categorie_id' => $categorie->id,
            'image' => $fichier,
        ]);
    }

    public function test_un_fichier_texte_est_refuse_comme_image(): void
    {
        $reponse = $this->envoyerAvecImage(
            UploadedFile::fake()->create('notes.txt', 10, 'text/plain')
        );

        $reponse->assertSessionHasErrors('image');
        $this->assertDatabaseCount('publications', 0);
    }

    public function test_un_pdf_est_refuse_comme_image(): void
    {
        $reponse = $this->envoyerAvecImage(
            UploadedFile::fake()->create('document.pdf', 10, 'application/pdf')
        );

        $reponse->assertSessionHasErrors('image');
        $this->assertDatabaseCount('publications', 0);
    }

    public function test_une_image_de_format_non_autorise_est_refusee(): void
    {
        $reponse = $this->envoyerAvecImage(
            UploadedFile::fake()->image('animation.gif')
        );

        $reponse->assertSessionHasErrors('image');
        $this->assertDatabaseCount('publications', 0);
    }

       public function test_une_image_trop_lourde_est_refusee(): void
    {
        // PNG plutôt que JPEG : ce PHP n'a pas le support JPEG dans GD
        $reponse = $this->envoyerAvecImage(
            UploadedFile::fake()->image('grande.png')->size(3000)
        );

        $reponse->assertSessionHasErrors('image');
        $this->assertDatabaseCount('publications', 0);
    }
}