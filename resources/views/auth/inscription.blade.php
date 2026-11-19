@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <h1>Créer un compte</h1>

    @if ($errors->any())
        <ul class="erreurs" style="color: #b00020;">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('inscription') }}" novalidate>
        @csrf
        <p>
            <label>Nom :</label><br>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Adresse e-mail :</label><br>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Mot de passe (8 caractères minimum) :</label><br>
            <input type="password" name="password">
            @error('password')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Confirmer le mot de passe :</label><br>
            <input type="password" name="password_confirmation">
        </p>
        <button type="submit">S'inscrire</button>
    </form>

    <p style="margin-top: 20px;">
        Déjà un compte ? <a href="{{ route('connexion') }}">Se connecter</a>
    </p>
@endsection