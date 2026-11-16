@extends('layouts.app')

@section('titre', 'Nouvelle publication')

@section('contenu')
    <h1>Créer une publication</h1>

    <form method="POST" action="{{ route('contenus.store') }}">
        @csrf
        <p>
            <label>Titre :</label><br>
            <input type="text" name="titre" value="{{ old('titre') }}" required style="width: 100%; padding: 8px;">
        </p>
        <p>
            <label>Catégorie :</label><br>
            <select name="categorie_id" required style="padding: 8px;">
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                @endforeach
            </select>
        </p>
        <p>
            <label>Contenu :</label><br>
            <textarea name="contenu" rows="5" required style="width: 100%; padding: 8px;">{{ old('contenu') }}</textarea>
        </p>
        <button type="submit" style="padding: 10px 20px;">Publier</button>
    </form>
@endsection
