# Rapport de qualité — MediaHub (semaine 11)

## 1. Tests automatisés

**48 tests, 119 assertions, 48 réussis (100 %)** — exécutés avec `php artisan test`.

Les tests utilisent une base SQLite en mémoire (configurée dans `phpunit.xml`) et le trait
`RefreshDatabase` : ils sont isolés de la base MySQL de développement.

| Fichier | Tests | Ce qui est vérifié |
|---|---|---|
| `AuthTest` | 4 | inscription, connexion, échec de connexion, déconnexion |
| `CategorieTest` | 7 | CRUD des catégories, gate `gerer-categories` (admin, auteur, visiteur), slug unique |
| `PublicationTest` | 11 | lecture publique, création, auteur pris dans la session, validation, policy (modifier/supprimer) |
| `Api/PublicationApiTest` | 14 | liste, détail, filtre, 401 sans jeton, 403, 404, 422, 201, 204 |
| `MediasTest` | 4 | rejet d'un fichier texte, d'un PDF, d'un GIF et d'une image trop lourde comme image |
| `CacheCategoriesTest` | 4 | mise en cache et invalidation (création, suppression) |
| `RequetesTest` | 1 | comptage des requêtes de la liste des publications |
| `RequetesAccueilTest` | 1 | comptage des requêtes de la page d'accueil |
| `ExampleTest` (Feature et Unit) | 2 | tests d'exemple de Laravel |

Cas d'échec couverts : mauvais mot de passe, visiteur non connecté, auteur modifiant la publication
d'un autre, jeton absent, données invalides.

## 2. Requêtes N+1

### Liste des publications (`/contenus`)

Problème : accéder à `$publication->categorie` et `$publication->auteur` dans une boucle
déclenche une requête par publication.

| Version | Requêtes (9 publications) |
|---|---|
| Sans chargement anticipé | 19 |
| Avec `with(['categorie', 'auteur'])` et `withCount('commentaires')` | 3 |
| Page réelle `/contenus` (3 + comptage de la pagination) | 4 |

La correction était déjà en place dans `ContenuController::index()` (semaine 5) ; le test la mesure
et empêche une régression.

### Page d'accueil (« Dernières publications »)

Un bloc affichant les 5 dernières publications a été ajouté à l'accueil, d'abord sans chargement
anticipé, puis corrigé.

| Version | Requêtes |
|---|---|
| Accueil sans publications (état initial) | 0 |
| Bloc ajouté, sans `with()` | 11 |
| Bloc ajouté, avec `with(['categorie', 'auteur'])` | 3 |

## 3. Cache

- **Donnée mise en cache :** la liste des catégories (`id`, `nom`) utilisée par les formulaires de
  création et de modification des publications.
- **Clé :** `categories.options`
- **Durée de validité :** 3600 secondes (1 heure).
- **Invalidation :** dans le modèle `Categorie`, les événements `saved` et `deleted` appellent
  `Cache::forget('categories.options')`. La création, la modification et la suppression sont donc
  couvertes, quel que soit le contrôleur qui les déclenche.
- **Choix :** la liste paginée avec compteurs (`/categories`) n'est pas mise en cache, car la
  pagination et le nombre de publications changent trop souvent.
- **Preuve :** un test vérifie que le second appel ne fait aucune requête SQL, et deux tests
  vérifient l'invalidation.

## 4. Limites connues

- Les tests s'exécutent sur SQLite, pas sur MySQL : un comportement propre à MySQL n'est pas couvert.
- L'extension GD de l'environnement de développement n'a pas le support JPEG ni WebP : les miniatures
  ne sont pas générées en local (le site retombe sur l'image originale). À vérifier au déploiement.
- Les mesures de requêtes utilisent le journal de requêtes de Laravel dans les tests, et non
  `DB::listen` dans `AppServiceProvider`, pour ne pas polluer `storage/logs/laravel.log`.