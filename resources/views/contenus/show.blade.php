@extends('layouts.app')

@section('titre', $publication->titre)

@section('contenu')
    <h1>{{ $publication->titre }}</h1>
    <p><strong>Auteur :</strong> {{ $publication->auteur->name }}</p>
    <p><strong>Catégorie :</strong> {{ $publication->categorie->nom }}</p>
    <p><strong>Tags :</strong>
    @forelse ($publication->tags as $tag)
        <span style="background: #eee; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; margin-right: 5px;">
            {{ $tag->nom }}
        </span>
    @empty
        <em>Aucun tag</em>
    @endforelse
</p>

    <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid #333;">
        {!! nl2br(e($publication->contenu)) !!}
    </div>

    <h3 style="margin-top: 40px;">Commentaires ({{ $publication->commentaires->count() }})</h3>

    @forelse ($publication->commentaires as $commentaire)
        <div class="carte" style="margin-bottom: 10px;">
            <p><strong>{{ $commentaire->auteur->name }}</strong> a dit :</p>
            <p>{{ $commentaire->contenu }}</p>
        </div>
    @empty
        <p>Aucun commentaire pour le moment.</p>
    @endforelse

      <p style="margin-top: 20px;">
        <a href="{{ route('contenus.index') }}">&larr; Retour à la liste</a>

        @can('update', $publication)
            | <a href="{{ route('contenus.edit', $publication) }}" style="color: blue;">Modifier</a>
        @endcan

        @can('delete', $publication)
            |
            <form method="POST" action="{{ route('contenus.destroy', $publication) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="color: red; background: none; border: none; cursor: pointer;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?');">Supprimer</button>
            </form>
        @endcan
    </p>
@endsection
