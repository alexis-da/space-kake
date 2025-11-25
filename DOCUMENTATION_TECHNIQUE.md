# Documentation Technique - Space Kake

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture du projet](#architecture-du-projet)
3. [Installation et démarrage](#installation-et-démarrage)
4. [Structure des dossiers](#structure-des-dossiers)
5. [Fonctionnalités principales](#fonctionnalités-principales)
6. [Guide d'utilisation](#guide-dutilisation)
7. [Architecture technique](#architecture-technique)

---

<a id="vue-densemble"></a>

## Vue d'ensemble

**Space Kake** est une application web de gestion et vente de gâteaux en ligne. Elle permet aux clients de :

-   Consulter un catalogue de gâteaux
-   Ajouter des gâteaux au panier
-   Passer des commandes
-   Laisser des avis sur les produits
-   Créer un compte client

Les administrateurs peuvent :

-   Ajouter, modifier, supprimer des gâteaux
-   Gérer les catégories
-   Consulter les commandes
-   Gérer les produits

---

<a id="architecture-du-projet"></a>

## Architecture du projet

**Stack technologique** :

-   **Backend** : Symfony 7.3 (PHP 8.2+)
-   **Frontend** : JavaScript, Twig (templating)
-   **Base de données** : Doctrine ORM, mySQL

**Architecture MVC (Model-View-Controller)** :

```
src/
├── Controller/      → Logique métier (routes)
├── Entity/          → Modèles de données
├── Form/            → Formulaires
└── Repository/      → Requêtes de base de données

templates/           → Vue Twig
assets/              → Ressources frontend
```

---

<a id="installation-et-démarrage"></a>

## Installation et démarrage

### Prérequis

-   PHP 8.2 ou supérieur
-   Composer
-   Un serveur web (WAMP)
-   MySQL ou SQLite

### Étapes d'installation

1. **Cloner le projet**

```bash
cd c:\wamp64\www\
git clone [URL_REPO] space-kake
cd space-kake
```

2. **Installer les dépendances**

```bash
composer install
```

3. **Configurer la base de données**

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

4. **Démarrer l'application**

```bash
# En développement
symfony serve

# Ou via WAMP (accès via http://localhost/space-kake)
```

5. **Construire les assets**

```bash
php bin/console asset-map:compile
```

---

<a id="structure-des-dossiers"></a>

## Structure des dossiers

```
space-kake/
│
├── src/                          # Code source principal
│   ├── Controller/                  # Contrôleurs (routes HTTP)
│   │   ├── CakesController.php      # Gestion des gâteaux
│   │   ├── CategoriesController.php # Gestion des catégories
│   │   ├── OrdersController.php     # Gestion des commandes
│   │   ├── ReviewsController.php    # Gestion des avis
│   │   ├── RegistrationController.php # Inscription
│   │   └── SecurityController.php   # Connexion/Déconnexion
│   │
│   ├── Entity/                      # Entités (modèles de données)
│   │   ├── Cakes.php               # Modèle gâteau
│   │   ├── Categories.php          # Modèle catégorie
│   │   ├── Clients.php             # Modèle client/utilisateur
│   │   ├── Orders.php              # Modèle commande
│   │   ├── CakeOrder.php           # Modèle de liaison (gâteau + commande)
│   │   └── Reviews.php             # Modèle avis
│   │
│   ├── Form/                        # Formulaires Symfony
│   │   ├── CakesType.php           # Formulaire ajout gâteau
│   │   ├── CategoriesType.php      # Formulaire catégories
│   │   ├── ReviewsType.php         # Formulaire avis
│   │   └── RegistrationFormType.php # Formulaire inscription
│   │
│   ├── Repository/                  # Requêtes spécialisées
│   │   ├── CakesRepository.php
│   │   ├── CategoriesRepository.php
│   │   ├── OrdersRepository.php
│   │   ├── ClientsRepository.php
│   │   └── ReviewsRepository.php
│   │
│   ├── Security/
│   │   └── LoginFormAuthenticator.php # Authentification
│   │
│   └── Kernel.php                   # Configuration Symfony
│
├── templates/                     # Vues Twig
│   ├── base.html.twig              # Template de base (header/footer)
│   ├── cakes/                       # Templates gâteaux
│   │   ├── index.html.twig         # Liste des gâteaux
│   │   ├── show.html.twig          # Détail d'un gâteau
│   │   ├── edit.html.twig          # Édition
│   │   ├── new.html.twig           # Création
│   │   ├── _form.html.twig         # Partial formulaire
│   │   └── _delete_form.html.twig  # Partial suppression
│   │
│   ├── categories/                  # Templates catégories
│   ├── orders/                      # Templates commandes
│   ├── reviews/                     # Templates avis
│   ├── registration/                # Templates inscription
│   ├── security/                    # Templates connexion
│   └── partials/                    # Composants réutilisables
│       ├── _header.html.twig
│       └── _footer.html.twig
│
├── assets/                        # Ressources frontend
│   ├── app.js                       # Point d'entrée JS
│   ├── bootstrap.js                 # Initialisation
│   ├── controllers.json             # Mapping Stimulus
│   ├── controllers/                 # Contrôleurs Stimulus
│   │   ├── csrf_protection_controller.js
│   │   └── hello_controller.js
│   ├── styles/
│   │   └── app.css                 # Styles CSS
│   └── vendor/                      # Librairies frontend
│
├── config/                        # Configuration
│   ├── bundles.php                  # Bundles Symfony
│   ├── services.yaml                # Services
│   ├── routes.yaml                  # Routage
│   ├── preload.php                  # Préchargement
│   └── packages/                    # Config par bundle
│       ├── framework.yaml           # Framework
│       ├── doctrine.yaml            # ORM
│       ├── security.yaml            # Sécurité/auth
│       ├── twig.yaml                # Templating
│       ├── asset_mapper.yaml        # Assets
│       └── ...
│
├── migrations/                    # Migrations BD
│   ├── Version20251022191331.php
│   ├── Version20251022221341.php
│   └── Version20251023082328.php
│
├── public/                        # Point d'entrée web
│   ├── index.php                    # Front controller
│   └── assets/
│       └── style.css                # Styles compilés
│
├── bin/                           # Exécutables
│   ├── console                      # Commandes Symfony
│   └── phpunit                      # Tests
│
├── tests/                         # Tests unitaires
│   └── bootstrap.php                # Configuration tests
│
├── composer.json                     # Dépendances PHP
├── phpunit.dist.xml                 # Configuration tests
└── importmap.php                     # Mapping imports ES6

```

---

<a id="fonctionnalités-principales"></a>

## Fonctionnalités principales

### 1. Gestion des gâteaux (Cakes)

-   Afficher liste des gâteaux
-   Ajouter un gâteau (admin)
-   Modifier un gâteau (admin)
-   Supprimer un gâteau (admin)
-   Consulter détails (image, prix, catégorie)

**Entité Cakes** :

```
- id (int) - Identifiant unique
- title (string) - Nom du gâteau
- description (string) - Description
- price (float) - Prix
- quantity (int) - Quantité disponible
- image (string) - URL de l'image
- category_id (FK) - Catégorie
- reviews (1-N) - Avis clients
- cakeOrders (1-N) - Commandes associées
```

### 2. Gestion des catégories

-   Créer une catégorie
-   Modifier une catégorie
-   Supprimer une catégorie
-   Chaque gâteau appartient à une catégorie

### 3. Système de commandes

-   Ajouter un gâteau au panier
-   Créer une commande
-   Consulter historique des commandes
-   Suivre le statut des commandes

**Entité CakeOrder** (liaison gâteau-commande) :

```
- id, cake_id, order_id
- Quantité par item
```

### 4. Système d'avis

-   Laisser un avis sur un gâteau
-   Noter sur 5 étoiles
-   Afficher les avis par gâteau

**Entité Reviews** :

```
- id, cake_id, client_id
- rating (1-5)
- comment (texte)
```

### 5. Authentification et sécurité

-   Inscription client
-   Connexion/Déconnexion
-   Rôles : Client, Admin
-   Protection CSRF sur formulaires
-   Contrôle d'accès par rôle

**Entité Clients** :

```
- id, email (unique)
- password (hashé)
- firstName, lastName
- isAdmin (bool)
```

---

<a id="guide-dutilisation"></a>

## Guide d'utilisation

### Pour un client

#### 1. S'inscrire

1. Cliquer sur "Inscription" en haut à droite
2. Remplir le formulaire (email, mot de passe, nom)
3. Valider
4. Redirection vers la page de connexion

#### 2. Se connecter

1. Cliquer sur "Connexion"
2. Entrer email et mot de passe
3. Cliquer "Se connecter"

#### 3. Consulter les gâteaux

1. Accueil → Catalogue gâteaux
2. Voir la liste avec images, prix, catégories
3. Cliquer sur un gâteau pour voir détails et avis

#### 4. Laisser un avis

1. Sur la page détail du gâteau
2. Remplir le formulaire "Laisser un avis"
3. Noter sur 5 étoiles
4. Ajouter un commentaire (optionnel)
5. Valider

#### 5. Commander

1. Ajouter des gâteaux au panier
2. Voir le résumé du panier
3. Valider la commande
4. Consulter historique des commandes

### Pour un administrateur

#### 1. Ajouter un gâteau

1. Aller dans "Admin" → "Gâteaux"
2. Cliquer "Ajouter un gâteau"
3. Remplir formulaire :
    - Nom (title)
    - Description
    - Prix
    - Quantité
    - Image (URL)
    - Catégorie
4. Valider

#### 2. Modifier un gâteau

1. Accéder à la liste des gâteaux
2. Cliquer sur "Éditer" sur le gâteau
3. Modifier les champs
4. Valider

#### 3. Supprimer un gâteau

1. Sur la page détail du gâteau
2. Cliquer "Supprimer"
3. Confirmer la suppression

#### 4. Gérer les catégories

1. Aller dans "Admin" → "Catégories"
2. Opérations CRUD (Créer/Lire/Modifier/Supprimer)

#### 5. Consulter les commandes

1. Aller dans "Admin" → "Commandes"
2. Voir liste des commandes
3. Cliquer pour voir détails

---

<a id="architecture-technique"></a>

## Architecture technique

### Pattern MVC avec Symfony

```
HTTP Request
    ↓
[Router] → Route matching
    ↓
[Controller] → Business logic (CakesController::index)
    ↓
[Entity] → ORM Doctrine (Cakes, Categories...)
    ↓
[Repository] → Database queries (CakesRepository)
    ↓
[Database] → MySQL/SQLite
    ↓
[View] → Twig templates rendering
    ↓
HTTP Response (HTML/JSON)
```

### Flux d'une requête

1. **Requête HTTP** → `GET /cakes`
2. **Router** → Identifie la route dans `config/routes.yaml`
3. **Contrôleur** → `CakesController::index()` s'exécute
4. **Repository** → `CakesRepository::findAll()` query BD
5. **Entité** → Hydratation des objets `Cake`
6. **Vue** → Rendu Twig `cakes/index.html.twig`
7. **Réponse** → HTML retourné au navigateur

### Sécurité

-   **Authentification** : `LoginFormAuthenticator` (email/password)
-   **Autorisations** : Rôles (ROLE_USER, ROLE_ADMIN)
-   **Hachage mot de passe** : Argon2 (secure)

---
