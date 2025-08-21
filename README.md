# BileMo API

BileMo est une API Symfony pour la gestion de produits et d’utilisateurs, sécurisée par JWT et documentée avec NelmioApiDoc.

## Prérequis
- [DDEV](https://ddev.readthedocs.io/en/stable/) installé
- Docker

## Installation

1. Clonez le projet :
   ```bash
   git clone <url-du-repo>
   cd BileMo
   ```
2. Initialisez DDEV :
   ```bash
   ddev config --project-type=symfony --docroot=public
   ddev start
   ```
3. Installez les dépendances :
   ```bash
   ddev composer install
   ```
4. Configurez les clés JWT :
   ```bash
   ddev exec mkdir -p config/jwt
   ddev exec openssl genrsa -out config/jwt/private.pem 4096
   ddev exec openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
   ```
5. Vérifiez les variables dans `.env` :
   ```env
   DATABASE_URL="mysql://db:db@db:3306/db?serverVersion=8.0.32&charset=utf8mb4"
   JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
   JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
   JWT_PASSPHRASE=
   ```
6. Exécutez les migrations et chargez les fixtures :
   ```bash
   ddev exec bin/console doctrine:migrations:migrate
   ddev exec bin/console doctrine:fixtures:load --no-interaction
   ```

## Utilisation

- Documentation API : [https://bilemo.ddev.site/api/doc](https://bilemo.ddev.site/api/doc)
- Authentification :
  - Endpoint : `/api/login_check`
  - Exemple :
    ```json
    {
      "username": "tata0@test.fr",
      "password": "password"
    }
    ```
  - Le token JWT obtenu doit être utilisé en tant que Bearer dans l’API Doc ou vos requêtes protégées.

## Données de test
- 5 administrateurs : `tata0@test.fr` à `tata4@test.fr` (mot de passe : `password`)
- 20 utilisateurs : `toto0@test.fr` à `toto19@test.fr` (mot de passe : `password`)

## Commandes utiles
- Lancer les migrations :
  ```bash
  ddev exec bin/console doctrine:migrations:migrate
  ```
- Charger les fixtures :
  ```bash
  ddev exec bin/console doctrine:fixtures:load --no-interaction
  ```

## Stack technique
- Symfony
- Doctrine ORM
- MySQL (via DDEV)
- JWT (LexikJWTAuthenticationBundle)
- NelmioApiDocBundle

## Licence
MIT
