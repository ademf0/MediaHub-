<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory, SoftDeletes;

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
protected $fillable = ['titre', 'contenu', 'categorie_id', 'user_id', 'image', 'fichier_media', 'type_media'];
}
