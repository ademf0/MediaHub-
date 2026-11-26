<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Categorie extends Model
{
    use HasFactory;

    private const OPTIONS_CACHE_KEY = 'categories.options.v2';

    protected $fillable = ['nom', 'slug'];

    protected static function booted(): void
    {
        // Toute création, modification ou suppression invalide le cache
        static::saved(fn () => Cache::forget(self::OPTIONS_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::OPTIONS_CACHE_KEY));
    }

    // Liste des catégories pour les formulaires : mise en cache pendant 1 heure
    public static function pourFormulaires()
    {
        return Cache::remember(self::OPTIONS_CACHE_KEY, 3600, function () {
            return static::query()->orderBy('nom')->get(['id', 'nom']);
        });
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}
