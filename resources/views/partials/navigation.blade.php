<nav style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
    <a href="{{ route('accueil') }}">MediaHub</a>
    <a href="{{ route('contenus.index') }}">Contenus</a>
    <a href="{{ route('categories.index') }}">Catégories</a>
    <a href="{{ route('a-propos') }}">À propos</a>

    @guest
        <a href="{{ route('connexion') }}">Connexion</a>
        <a href="{{ route('inscription') }}">Inscription</a>
    @endguest

    @auth
        <span>Connecté : {{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('deconnexion') }}" style="display: inline;">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>
    @endauth
</nav>