@extends('layouts.app')

@section('titre', 'Catégories')

@section('contenu')
    <h1>Liste des catégories</h1>

    @can('gerer-categories')
        <p><a href="{{ route('categories.create') }}">+ Ajouter une catégorie</a></p>
    @endcan

    <ul>
        @foreach ($categories as $categorie)
            <li style="margin-bottom: 10px;">
                {{ $categorie->nom }} ({{ $categorie->publications_count }} publications)

                <!-- Lien de modification (ouvert aux utilisateurs connectés) -->
                @auth
                    <a href="{{ route('categories.edit', $categorie) }}" style="margin-left: 10px; color: blue;">Modifier</a>
                @endauth

                <!-- Suppression : administrateurs uniquement -->
                @can('gerer-categories')
                    <form method="POST" action="{{ route('categories.destroy', $categorie) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer;" onclick="return confirm('Êtes-vous sûr ?');">Supprimer</button>
                    </form>
                @endcan
            </li>
        @endforeach
    </ul>

    <div style="margin-top: 20px;">
        {{ $categories->links() }}
    </div>
@endsection
