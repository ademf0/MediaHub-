@extends('layouts.app')

@section('titre', 'Nouvelle publication')

@section('contenu')
    <h1>Créer une publication</h1>

    @if ($errors->any())
        <ul class="erreurs" style="color: #b00020;">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

        <form method="POST" action="{{ route('contenus.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <p>
            <label>Titre :</label><br>
            <input type="text" name="titre" value="{{ old('titre') }}" style="width: 100%; padding: 8px;">
            @error('titre')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Catégorie :</label><br>
            <select name="categorie_id" style="padding: 8px;">
                <option value="">-- Choisir une catégorie --</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
            @error('categorie_id')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Contenu :</label><br>
            <textarea name="contenu" rows="5" style="width: 100%; padding: 8px;">{{ old('contenu') }}</textarea>
            @error('contenu')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
                <p>
            <label>Image (JPG, PNG ou WEBP, 2 Mo maximum) :</label><br>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            @error('image')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Audio ou vidéo (MP3, WAV ou MP4, 20 Mo maximum) :</label><br>
            <input type="file" name="fichier_media" accept="audio/*,video/mp4">
            @error('fichier_media')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <button type="submit" style="padding: 10px 20px;">Publier</button>
    </form>
@endsection
