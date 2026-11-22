<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->unique()->sentence(6),
            'contenu' => fake()->paragraphs(3, true),
            // Réutilise une catégorie existante, ou en crée une si la base est vide
            'categorie_id' => fn () => Categorie::query()->inRandomOrder()->value('id')
                ?? Categorie::factory()->create()->id,
            // Crée un utilisateur automatiquement
            'user_id' => User::factory(),
        ];
    }
}