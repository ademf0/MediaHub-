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
        ];
    }
}