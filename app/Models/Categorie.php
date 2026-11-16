<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADD THIS
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory; // <--- AND ADD THIS
    public function publications()
{
    return $this->hasMany(Publication::class);
}
protected $fillable = ['nom', 'slug'];
}
