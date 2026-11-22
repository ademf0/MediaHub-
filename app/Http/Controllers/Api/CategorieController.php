<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategorieResource;
use App\Models\Categorie;

class CategorieController extends Controller
{
    // GET /api/categories : liste des catégories avec le nombre de publications
    public function index()
    {
        return CategorieResource::collection(
            Categorie::withCount('publications')->get()
        );
    }
}
