# API REST MediaHub

URL de base (développement) : `http://127.0.0.1:8000/api`

Toutes les requêtes doivent envoyer l'en-tête `Accept: application/json`.
Les routes d'écriture exigent aussi `Authorization: Bearer <jeton>`.

## Récapitulatif

| Méthode | URL | Authentification | Description |
|---|---|---|---|
| POST | `/api/connexion` | Non | Obtenir un jeton |
| GET | `/api/categories` | Non | Lister les catégories |
| GET | `/api/publications` | Non | Lister les publications (paginé, filtrable) |
| GET | `/api/publications/{id}` | Non | Afficher une publication |
| POST | `/api/publications` | Jeton | Créer une publication |
| PUT / PATCH | `/api/publications/{id}` | Jeton (auteur ou admin) | Modifier une publication |
| DELETE | `/api/publications/{id}` | Jeton (auteur ou admin) | Supprimer une publication |

## Codes de réponse

| Code | Signification |
|---|---|
| 200 | Lecture ou modification réussie |
| 201 | Création réussie |
| 204 | Suppression réussie (pas de corps) |
| 401 | Jeton absent ou invalide |
| 403 | Action interdite (publication d'un autre auteur) |
| 404 | Ressource introuvable |
| 422 | Données invalides |

## Obtenir un jeton

`POST /api/connexion`

Requête :

```json
{ "email": "test1@mediahub.com", "password": "motdepasse123" }
```

Réponse 200 :

```json
{ "token": "1|xxxxxxxxxxxxxxxx" }
```

Réponse 401 (mauvais identifiants) :

```json
{ "message": "Identifiants incorrects." }
```

## Lister les catégories

`GET /api/categories`

Réponse 200 :

```json
{
  "data": [
    { "id": 1, "nom": "Vidéo", "slug": "video", "publications": 6 }
  ]
}
```

## Lister les publications

`GET /api/publications`

Paramètres facultatifs :

| Paramètre | Description |
|---|---|
| `page` | Numéro de page (10 publications par page) |
| `categorie_id` | Ne garder que les publications de cette catégorie |

Exemple : `GET /api/publications?categorie_id=2&page=1`

Réponse 200 (extrait) :

```json
{
  "data": [
    {
      "id": 23,
      "titre": "titre de projet",
      "contenu": "regardez ce pdf qui contient des informations .",
      "categorie": "Image",
      "auteur": "Test Un",
      "image": null,
      "type_media": null,
      "media": null,
      "publie_le": "2026-11-22"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 10, "total": 22 }
}
```

## Afficher une publication

`GET /api/publications/{id}`

Réponse 200 : un objet `data` avec les mêmes champs que ci-dessus.

Réponse 404 : publication inexistante ou supprimée.

## Créer une publication

`POST /api/publications` — jeton requis.

Requête :

```json
{
  "titre": "Publication creee par API",
  "contenu": "Contenu de test cree via l'API REST.",
  "categorie_id": 1
}
```

Réponse 201 : l'objet `data` de la publication créée.

Réponse 401 : jeton absent.

```json
{ "message": "Unauthenticated." }
```

Réponse 422 : données invalides.

```json
{
  "message": "Le titre doit contenir au moins 10 caractères. (and 2 more errors)",
  "errors": {
    "titre": ["Le titre doit contenir au moins 10 caractères."],
    "contenu": ["Le contenu est obligatoire."],
    "categorie_id": ["Veuillez choisir une catégorie."]
  }
}
```

Règles : `titre` (10 à 255 caractères, unique), `contenu` (20 caractères minimum), `categorie_id` (catégorie existante). L'auteur est celui du jeton.

## Modifier une publication

`PUT /api/publications/{id}` — jeton requis, auteur ou administrateur.

Mêmes champs et mêmes règles que la création.

Réponse 200 : l'objet `data` mis à jour.

Réponse 403 : la publication appartient à un autre utilisateur.

```json
{ "message": "This action is unauthorized." }
```

## Supprimer une publication

`DELETE /api/publications/{id}` — jeton requis, auteur ou administrateur.

Réponse 204 : suppression réussie, sans corps.

Réponse 401 : jeton absent. Réponse 403 : publication d'un autre utilisateur.

## Limites

L'API gère les champs texte. Les fichiers (image, audio, vidéo, PDF) sont téléversés depuis l'interface web.