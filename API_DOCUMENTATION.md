# Documentation Technique de l'API BileMo

## Présentation
L'API BileMo permet la gestion de produits et d'utilisateurs pour les clients administrateurs. Elle est développée avec Symfony, sécurisée par JWT, et documentée via NelmioApiDocBundle.

## Authentification
- **JWT** : Toutes les routes protégées nécessitent un token JWT.
- **Endpoint d'authentification** :
  - `POST /api/login_check`
  - Body :
    ```json
    {
      "username": "tata0@test.fr",
      "password": "password"
    }
    ```
  - Le token reçu doit être utilisé dans le header `Authorization: Bearer <token>`.

## Endpoints Principaux

### Utilisateurs
- **Lister les utilisateurs rattachés à l'admin connecté**
  - `GET /api/users`
  - Retourne uniquement les utilisateurs dont le référent est l'admin connecté.

- **Créer un utilisateur**
  - `POST /api/user`
  - Body :
    ```json
    {
      "email": "nouveluser@test.fr",
      "password": "password",
      "roles": ["ROLE_USER"]
    }
    ```
  - Le référent est automatiquement l'admin connecté.

- **Voir le détail d'un utilisateur**
  - `GET /api/user/{id}`
  - Accessible uniquement si l'utilisateur est rattaché à l'admin connecté.

- **Supprimer un utilisateur**
  - `DELETE /api/user/{id}`
  - Accessible uniquement si l'utilisateur est rattaché à l'admin connecté.

### Produits
- **Lister tous les produits**
  - `GET /api/products`

## Sécurité
- Les endpoints `/api/users`, `/api/user`, `/api/products` sont protégés par JWT.
- Seuls les admins peuvent créer et gérer des utilisateurs.
- Chaque utilisateur créé est automatiquement rattaché à l'admin qui fait la requête (champ `referent`).

## Structure des Entités

### User
- `id`: int
- `email`: string
- `roles`: array (json)
- `password`: string (hashé)
- `token`: string (JWT)
- `referent`: User (admin référent)

### Customer
- `id`: int
- `customer`: User (admin)
- `user`: User (utilisateur rattaché)

### Product
- `id`: int
- `name`: string
- `description`: string

## Fixtures de test
- 5 admins : `tata0@test.fr` à `tata4@test.fr` (mot de passe : `password`)
- 20 utilisateurs : `toto0@test.fr` à `toto19@test.fr` (mot de passe : `password`)

## Documentation interactive
- Accès à la documentation interactive : `/api/doc`
- Possibilité de tester les endpoints et de générer des requêtes JSON directement dans l'interface.

## Stack technique
- Symfony
- Doctrine ORM
- MySQL (via DDEV)
- JWT (LexikJWTAuthenticationBundle)
- NelmioApiDocBundle

## Contact & Support
Pour toute question technique, contactez l'équipe BileMo ou consultez la documentation du projet.

---
Ce document est à destination des développeurs et intégrateurs souhaitant utiliser ou étendre l'API BileMo.
