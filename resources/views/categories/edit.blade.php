@extends('layouts.app')

@section('titre', 'Modifier la catégorie')

@section('contenu')
    <h1>Modifier la catégorie</h1>

    @if ($errors->any())
        <ul class="erreurs" style="color: #b00020;">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('categories.update', $categorie) }}" novalidate>
        @csrf
        @method('PUT')
        <p>
            <label>Nom de la catégorie :</label><br>
            <input type="text" name="nom" value="{{ old('nom', $categorie->nom) }}">
            @error('nom')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Slug :</label><br>
            <input type="text" name="slug" value="{{ old('slug', $categorie->slug) }}">
            @error('slug')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
                <p>
            <label>Document PDF privé (5 Mo maximum, visible par vous et les administrateurs) :</label><br>
            @isset($publication)
                @if ($publication->document)
                    <small>Un document est déjà associé. Choisis un fichier seulement pour le remplacer.</small><br>
                @endif
            @endisset
            <input type="file" name="document" accept="application/pdf">
            @error('document')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <button type="submit">Enregistrer les modifications</button>
    </form>
@endsection