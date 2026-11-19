@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
    <h1>Connexion</h1>

    @if ($errors->any())
        <ul class="erreurs" style="color: #b00020;">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('connexion') }}" novalidate>
        @csrf
        <p>
            <label>Adresse e-mail :</label><br>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>Mot de passe :</label><br>
            <input type="password" name="password">
            @error('password')
                <span class="erreur-champ" style="color: #b00020;">{{ $message }}</span>
            @enderror
        </p>
        <button type="submit">Se connecter</button>
    </form>

    <p style="margin-top: 20px;">
        Pas encore de compte ? <a href="{{ route('inscription') }}">S'inscrire</a>
    </p>
@endsection