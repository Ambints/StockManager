# StockManager — Application PHP MVC

Système de gestion de stock complet en **PHP procédural/POO**, **PDO PostgreSQL**, **Bootstrap Icons** et **Vanilla JS**.

---

## Structure du projet

```
stock_mvc/
├── .htaccess                   ← Réécriture Apache (racine)
├── README.md
│
├── config/
│   ├── config.php              ← Paramètres app, routes
│   └── database.php            ← Connexion PDO PostgreSQL (singleton)
│
├── core/                       ← Classes de base (MVC)
│   ├── Router.php              ← Routeur Front Controller
│   ├── Controller.php          ← Contrôleur abstrait (render, redirect, auth…)
│   └── Model.php               ← Modèle abstrait (CRUD, pagination, PDO)
│
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProduitController.php
│   │   ├── MouvementController.php
│   │   ├── AlerteController.php
│   │   ├── UtilisateurController.php
│   │   ├── CategorieController.php
│   │   └── FournisseurController.php
│   │
│   ├── models/
│   │   ├── ProduitModel.php
│   │   ├── MouvementModel.php
│   │   ├── AlerteModel.php
│   │   ├── UtilisateurModel.php
│   │   ├── CategorieModel.php
│   │   └── FournisseurModel.php
│   │
│   └── views/
│       ├── layouts/
│       │   └── main.php        ← Layout principal (sidebar + topbar)
│       ├── auth/
│       │   └── login.php
│       ├── dashboard/
│       │   └── index.php
│       ├── produits/
│       │   ├── index.php
│       │   ├── form.php        ← Création + édition
│       │   └── show.php
│       ├── mouvements/
│       │   ├── index.php
│       │   └── form.php
│       ├── alertes/
│       │   └── index.php
│       ├── utilisateurs/
│       │   ├── index.php
│       │   └── form.php
│       ├── categories/
│       │   └── index.php
│       ├── fournisseurs/
│       │   └── index.php
│       └── errors/
│           └── 404.php
│
├── public/                     ← Seul dossier exposé par Apache
│   ├── .htaccess
│   ├── index.php               ← Front controller
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
│
└── sql/
    └── schema_gestion_stock.sql
```

---

## Installation

### 1. Prérequis

| Outil      | Version minimale |
|-----------|-----------------|
| PHP        | 8.1+            |
| PostgreSQL | 14+             |
| Apache     | 2.4+ (mod_rewrite activé) |
| Extension  | `pdo_pgsql`     |

### 2. Cloner / copier le projet

```bash
# Placez le dossier stock_mvc dans la racine de votre serveur web
# Exemple : /var/www/html/stock_mvc  ou  C:/xampp/htdocs/stock_mvc
```

### 3. Créer la base de données PostgreSQL

```bash
# Connectez-vous à PostgreSQL
psql -U postgres

# Créez la base
CREATE DATABASE stock_boutique;
\c stock_boutique

# Importez le schéma
\i /chemin/vers/stock_mvc/sql/schema_gestion_stock.sql
```

### 4. Configurer la connexion

Ouvrez `config/database.php` et modifiez :

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'stock_boutique');
define('DB_USER', 'postgres');
define('DB_PASS', 'votre_mot_de_passe');   // ← Ici
```

### 5. Configurer l'URL de base

Ouvrez `config/config.php` et ajustez `APP_URL` selon votre environnement :

```php
// Développement local (dossier stock_mvc dans htdocs)
define('APP_URL', 'http://localhost/stock_mvc/public');

// Domaine dédié
define('APP_URL', 'https://stock.votredomaine.mg/public');

// Ou si public/ est la racine du vhost
define('APP_URL', 'https://stock.votredomaine.mg');
```

### 6. Activer mod_rewrite (Apache)

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Assurez-vous que votre VirtualHost autorise `.htaccess` :

```apache
<Directory "/var/www/html/stock_mvc">
    AllowOverride All
    Require all granted
</Directory>
```

### 7. Créer le premier compte administrateur

Le schéma SQL insère un compte par défaut avec un **hash placeholder**.
Vous devez le remplacer par un vrai hash bcrypt PHP.

**Option A — Script PHP rapide :**

```php
<?php
echo password_hash('VotreMotDePasse123!', PASSWORD_BCRYPT, ['cost' => 12]);
```

Puis en psql :

```sql
UPDATE utilisateurs
SET mot_de_passe_hash = '$2y$12$...(votre hash)...'
WHERE email = 'admin@boutique.mg';
```

**Option B — Créer directement en psql :**

```sql
-- Remplacez le hash ci-dessous par celui généré par password_hash()
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, role)
VALUES ('Admin', 'Super', 'admin@boutique.mg', '$2y$12$VOTRE_HASH_ICI', 'admin');
```

---

## Accès

| URL                                     | Page              |
|----------------------------------------|-------------------|
| `http://localhost/stock_mvc/public`     | Page de connexion |
| `.../dashboard`                         | Tableau de bord   |
| `.../produits`                          | Gestion produits  |
| `.../mouvements`                        | Journal mouvements|
| `.../alertes`                           | Alertes stock     |
| `.../utilisateurs`                      | Utilisateurs (admin)|
| `.../categories`                        | Catégories        |
| `.../fournisseurs`                      | Fournisseurs      |

---

## Sécurité implémentée

| Mécanisme                  | Détail |
|---------------------------|--------|
| **Injections SQL**        | 100% requêtes préparées PDO (`bindValue`) |
| **XSS**                   | `htmlspecialchars()` sur toutes les sorties HTML |
| **CSRF**                  | Token aléatoire (`bin2hex(random_bytes(32))`) sur tous les formulaires POST |
| **Session fixation**      | `session_regenerate_id(true)` après login et toutes les 30 min |
| **Mots de passe**         | `password_hash()` bcrypt, coût 12 |
| **Accès par rôle**        | `requireRole('admin', 'gestionnaire')` dans chaque contrôleur |
| **Cookies session**       | `httponly`, `samesite=Lax`, `secure` si HTTPS |
| **Listing répertoires**   | `Options -Indexes` dans `.htaccess` |
| **Transactions SQL**      | `BEGIN/COMMIT/ROLLBACK` pour les mouvements de stock |

---

## Rôles utilisateur

| Rôle          | Accès |
|--------------|-------|
| `admin`       | Tout (y compris gestion utilisateurs, suppression) |
| `gestionnaire`| Produits, mouvements, alertes, catégories, fournisseurs |
| `vendeur`     | Lecture produits, enregistrement mouvements, consultation alertes |

---

## Personnalisation

### Modifier le fuseau horaire
Dans `config/database.php`, ligne :
```php
self::$instance->exec("SET TIME ZONE 'Indian/Antananarivo'");
```

### Modifier la pagination
Dans `config/config.php` :
```php
define('ITEMS_PER_PAGE', 20);
```

### Ajouter une nouvelle route
Dans `config/config.php`, tableau `ROUTES` :
```php
'ma-section/action' => ['controller' => 'MonController', 'action' => 'maMethode'],
```

---

## Démarrage rapide (test local avec PHP built-in server)

```bash
cd stock_mvc/public
php -S localhost:8080

# Ouvrez http://localhost:8080
```

> Note : Le serveur built-in PHP ne supporte pas `.htaccess`.
> Utilisez Apache/Nginx pour la réécriture d'URL en production.
