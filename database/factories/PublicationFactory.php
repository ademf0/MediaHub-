<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publication>
 */
class PublicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(6),
            'contenu' => fake()->paragraphs(3, true),
            // On prend une catégorie au hasard parmi celles existantes
            'categorie_id' => Categorie::inRandomOrder()->first()?->id ?? Categorie::factory(),            // On crée un nouvel utilisateur factice pour chaque publication
            'user_id' => User::factory(),
        ];
    }
}
