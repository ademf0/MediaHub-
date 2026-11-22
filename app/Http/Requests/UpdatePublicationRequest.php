<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => [
                'required',
                'string',
                'min:10',
                'max:255',
                Rule::unique('publications', 'titre')->ignore($this->route('contenu')),
            ],
            'contenu' => 'required|string|min:20',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'fichier_media' => 'nullable|file|mimes:mp3,mp4,wav|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins 10 caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'titre.unique' => 'Une publication avec ce titre existe déjà.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'contenu.min' => 'Le contenu doit contenir au moins 20 caractères.',
            'categorie_id.required' => 'Veuillez choisir une catégorie.',
            'categorie_id.exists' => "La catégorie choisie n'existe pas.",
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => "L'image doit être au format JPG, PNG ou WEBP.",
            'image.max' => "L'image ne doit pas dépasser 2 Mo.",
            'image.uploaded' => "L'image n'a pas pu être envoyée (fichier trop volumineux).",
            'fichier_media.mimes' => 'Le média doit être au format MP3, WAV ou MP4.',
            'fichier_media.max' => 'Le média ne doit pas dépasser 20 Mo.',
            'fichier_media.uploaded' => "Le média n'a pas pu être envoyé (fichier trop volumineux).",
        ];
    }
}