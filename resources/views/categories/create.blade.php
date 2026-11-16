@extends('layouts.app')

@section('titre', 'Nouvelle catégorie')

@section('contenu')
    <h1>Créer une catégorie</h1>

    @if ($errors->any())
        <ul class="erreurs" style="color: #b00020;">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('categories.store') }}" novalidate>
        @csrf
        <p>
            <label>Nom de la catégorie :</label><br>
            <input type="text" name="nom" value="{{ old('nom') }}">
            @error('nom')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Slug (ex: video) :</label><br>
            <input type="text" name="slug" value="{{ old('slug') }}">
            @error('slug')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <button type="submit">Créer</button>
    </form>
@endsection
