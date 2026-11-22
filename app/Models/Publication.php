<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['titre', 'contenu', 'categorie_id', 'user_id', 'image', 'fichier_media', 'type_media'];

    protected static function booted(): void
    {
        // Suppression définitive : on efface aussi les fichiers du disque
        static::forceDeleting(function (Publication $publication) {
            if ($publication->image) {
                Storage::disk('public')->delete($publication->image);
                Storage::disk('public')->delete($publication->cheminMiniature());
            }
            if ($publication->fichier_media) {
                Storage::disk('public')->delete($publication->fichier_media);
            }
        });
    }

    // Chemin de la miniature associée à l'image (null s'il n'y a pas d'image)
    public function cheminMiniature(): ?string
    {
        return $this->image
            ? 'publications/miniatures/' . pathinfo($this->image, PATHINFO_FILENAME) . '.jpg'
            : null;
    }

    // URL de la miniature, ou de l'image originale si la miniature n'existe pas
    public function getUrlMiniatureAttribute(): ?string
    {
        $miniature = $this->cheminMiniature();

        if ($miniature && Storage::disk('public')->exists($miniature)) {
            return Storage::url($miniature);
        }

        return $this->image ? Storage::url($this->image) : null;
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}