@extends('layouts.app')

@section('titre', 'Accueil')

@section('contenu')
    <h1>Bienvenue sur MediaHub</h1>
    <p>La plateforme de gestion et de diffusion de contenus multimédias.</p>

    <h2 style="margin-top: 30px;">Dernières publications</h2>

    @forelse ($dernieres as $publication)
        <p>
            <a href="{{ route('contenus.show', $publication) }}">{{ $publication->titre }}</a>
            — {{ $publication->categorie->nom }}, par {{ $publication->auteur->name }}
        </p>
    @empty
        <p>Aucune publication pour le moment.</p>
    @endforelse
@endsection
