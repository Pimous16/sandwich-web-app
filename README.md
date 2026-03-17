# Sandwich Web App

## Description
Sandwich Web App est une application web permettant aux utilisateurs de consulter des sandwichs disponibles, de passer des commandes, et de gérer leurs comptes. L'application inclut également des fonctionnalités avancées comme la génération de QR codes pour identifier ou suivre les commandes.

---

## Fonctionnalités principales

### 1. Gestion des utilisateurs
- **Inscription** : Les utilisateurs peuvent créer un compte via la page `signup.php`.
- **Connexion** : Les utilisateurs peuvent se connecter via la page `login.php`.
- **Déconnexion** : Une option de déconnexion est disponible via `logout.php`.

### 2. Gestion des commandes
- Les utilisateurs peuvent passer des commandes via la page `commandes.php`.
- Les commandes sont enregistrées dans une base de données.

### 3. Gestion des sandwichs
- Les détails des sandwichs sont affichés dans la page `sandwich_detail/sandwich.php`.
- Les données des sandwichs sont stockées dans un fichier JSON (`sandwiches.json`) ou dans une base de données.

### 4. Génération de QR codes
- Les QR codes sont générés via `generate_qr.php` pour identifier les commandes ou les sandwichs.
- Utilisation des bibliothèques `bacon-qr-code` et `endroid/qr-code`.

### 5. Base de données
- La connexion à la base de données est gérée par `db.php`.
- Un fichier SQL (`sandwich_projet-po_cyril.sql`) est inclus pour initialiser la base de données.

### 6. Design et mise en page
- Les fichiers CSS (`style.css`, `commande.css`, etc.) définissent le style de l'application.

---

## Structure du projet

### Racine
- **README.md** : Documentation du projet.
- **sandwich_projet-po_cyril.sql** : Script SQL pour initialiser la base de données.

### Dossier `sandwich/`
- **index.php** : Page d'accueil ou point d'entrée principal.
- **commandes.php** : Gestion des commandes.
- **crud.php** : Opérations CRUD (Create, Read, Update, Delete).
- **db.php** : Connexion à la base de données.
- **generate_qr.php** : Génération de QR codes.
- **login.php** : Page de connexion.
- **signup.php** : Page d'inscription.
- **logout.php** : Déconnexion des utilisateurs.
- **probleme.php** : Gestion des erreurs ou signalement de problèmes.
- **sandwiches.json** : Données des sandwichs.
- **style.css** et **commande.css** : Fichiers de style.

### Dossier `sandwich/sandwich_detail/`
- **data.php** : Fournit des données sur les sandwichs.
- **sandwich.php** : Affiche les détails d'un sandwich spécifique.
- **detail.css** : Fichier de style pour la page de détails.

### Dossier `vendor/`
- Contient les dépendances PHP gérées par Composer :
  - **bacon-qr-code** : Bibliothèque pour générer des QR codes.
  - **endroid/qr-code** : Autre bibliothèque pour les QR codes.
  - **composer/** : Fichiers générés par Composer pour l'autoloading.

---

## Technologies utilisées
- **PHP** : Langage principal pour la logique serveur.
- **Composer** : Gestionnaire de dépendances PHP.
- **JSON** : Format utilisé pour stocker des données (ex. : `sandwiches.json`).
- **Bibliothèques QR Code** :
  - `bacon-qr-code`
  - `endroid/qr-code`

---

## Installation

### Prérequis
- Serveur web local (ex. : XAMPP, WAMP).
- PHP installé.
- Composer installé.

### Étapes
1. Clonez le dépôt dans votre serveur local :
   ```bash
<<<<<<< HEAD
   git clone <url-du-repo>```

### Installation

=======
   git clone <url-du-repo>

### Installation
>>>>>>> 6ec1b836a3949b908b1e84973fc4ba8eec9af4f6
1. **Placez le projet dans le dossier racine de votre serveur web**
   - Exemple : `htdocs` pour XAMPP.

2. **Installez les dépendances avec Composer** :
   ```bash
   composer install
   ```

3. **Importez le fichier SQL dans votre base de données** :
   - Ouvrez votre outil de gestion de base de données (ex. : phpMyAdmin).
   - Créez une nouvelle base de données.
   - Importez le fichier `sandwich_projet-po_cyril.sql`.

4. **Configurez la connexion à la base de données dans `db.php`**.

5. **Lancez l'application dans votre navigateur** :
   ```
   http://localhost/sandwich-web-app/sandwich/index.php
<<<<<<< HEAD
   ```
=======
   ```
>>>>>>> 6ec1b836a3949b908b1e84973fc4ba8eec9af4f6
