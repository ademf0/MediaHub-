<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_sinscrire(): void
    {
        $reponse = $this->post('/inscription', [
            'name' => 'Amel',
            'email' => 'amel@exemple.com',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse',
        ]);

        $reponse->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'amel@exemple.com']);
    }

    public function test_connexion_avec_identifiants_incorrects(): void
    {
        User::factory()->create([
            'email' => 'existant@exemple.com',
            'password' => bcrypt('bonmotdepasse'),
        ]);

        $reponse = $this->post('/connexion', [
            'email' => 'existant@exemple.com',
            'password' => 'faux',
        ]);

        $reponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $utilisateur = User::factory()->create([
            'email' => 'existant@exemple.com',
            'password' => bcrypt('bonmotdepasse'),
        ]);

        $reponse = $this->post('/connexion', [
            'email' => 'existant@exemple.com',
            'password' => 'bonmotdepasse',
        ]);

        $reponse->assertRedirect('/');
        $this->assertAuthenticatedAs($utilisateur);
    }

    public function test_un_utilisateur_connecte_peut_se_deconnecter(): void
    {
        $utilisateur = User::factory()->create();

        $reponse = $this->actingAs($utilisateur)->post('/deconnexion');

        $reponse->assertRedirect('/');
        $this->assertGuest();
    }
}