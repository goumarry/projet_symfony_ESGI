# 🛡️ NetBook - Exercice 4 & 5

## 🚀 Fonctionnalités Clés

### 1. Authentification & Gestion des Utilisateurs
* **Inscription complète :** Création de compte avec hachage sécurisé du mot de passe.
* **Vérification d'email (Mode Dev) :**
    * Le système envoie un email avec un lien unique pour valider le compte.
    * **Note importante :** En local, les mails ne partent pas réellement. Ils sont interceptés par Symfony et consultables via l'icône "Email" dans la barre d'outils en bas de l'écran (Web Toolbar).
* **User Checker :** J'ai créé un `UserChecker` personnalisé qui bloque la connexion tant que l'email n'est pas vérifié, même si le mot de passe est correct.
* **Connexion / Déconnexion :** Utilisation du système de login natif de Symfony.
* **Se souvenir de moi :** Option "Remember me" (cookie persistant) activable via une case à cocher.
* **Mot de passe oublié :** Gestion complète : demande de réinitialisation, envoi de token par mail, et formulaire de changement de mot de passe.

### 2. Sécurité & Contrôle d'Accès
* **Hiérarchie des Rôles :** Distinction claire entre `ROLE_USER` et `ROLE_ADMIN`. L'admin possède automatiquement tous les droits d'un utilisateur classique.
* **Firewall Strict :**
    * **Interface Web :** Tout le site est verrouillé par défaut. Seules les pages d'accueil, de login et d'inscription sont publiques.
    * **API :** Les routes de lecture (`GET`) sont accessibles aux utilisateurs, mais les routes de modification (`POST`, `PUT`, `DELETE`) sont réservées aux admins.
* **Protection CSRF :** Activée sur tous les formulaires pour empêcher les attaques inter-sites.
* **Monitoring :** Chaque tentative de connexion (réussie ou échouée) est enregistrée en base de données avec l'adresse IP. Cela permet de détecter les tentatives de piratage (Brute Force).

### 3. API RESTful & Documentation
* **Endpoints CRUD :** API complète pour gérer les livres (`/api/books`) avec les méthodes `GET`, `POST`, `PUT`, `DELETE`.
* **Format Standard :** L'API renvoie toujours du JSON propre et utilise les bons codes HTTP (201 pour la création, 404 si non trouvé, 422 si données invalides).
* **Documentation OpenAPI :** J'ai intégré **Swagger UI** (`NelmioApiDocBundle`). La documentation interactive est accessible sur `/api/doc`.
* **Gestion des Erreurs :** J'ai mis en place un "Listener" global. Il intercepte toutes les erreurs du site (404, 500...) et les transforme en JSON lisible pour éviter d'envoyer du HTML ou des détails techniques sensibles aux clients de l'API.

### 4. Interfaces (Web & Client Léger)
* **Administration Générale :** Un tableau de bord sécurisé pour voir la liste des utilisateurs et surveiller les logs de connexion.
* **Catalogue "Headless" :** Une page qui charge les livres dynamiquement via **jQuery AJAX**. Cela permet d'afficher le contenu sans recharger toute la page.
* **Dashboard Admin Livres :** Une interface complète en AJAX pour gérer les livres :
    * Ajout et suppression sans rechargement.
    * Édition directe dans le tableau ("In-line editing").
    * **Algorithme JS :** Un script génère automatiquement un ISBN valide (avec calcul de la clé de contrôle) pour faciliter les tests.

---

## 🏗️ Choix Architecturaux et Techniques

### 1. Architecture API (DTO Pattern)
J'ai choisi de ne pas exposer directement mes Entités (Base de données) à l'API. J'utilise des objets intermédiaires (DTO) :
* **InputDTO :** Sert à valider strictement les données que l'on reçoit (Sécurité).
* **OutputDTO :** Sert à choisir exactement quelles données on renvoie (Confidentialité et formatage).
  Cela évite les problèmes de boucles infinies et protège la structure de la base de données.

### 2. Logique Métier et Services
Pour garder mes Contrôleurs légers ("Thin Controllers"), j'ai déporté la logique dans des Services :
* **`AccountService` :** Gère toute la mécanique d'inscription (hachage, token, mail).
* **`BookService` :** Gère la transformation des données et l'enregistrement des livres.

### 3. Qualité & Tests Automatisés
J'ai écrit des tests pour garantir que le code fonctionne :
* **Tests Unitaires :** Vérifient le fonctionnement interne des Services.
* **Tests Fonctionnels :** Simulent de vraies requêtes HTTP sur l'API pour vérifier les codes de retour et la sécurité.
* **Environnement de Test (SQLite) :** Pour les tests, j'utilise une base de données **SQLite** temporaire (fichier `.db`). Elle est effacée et recréée à chaque test. Cela permet de faire des tests rapides sans risquer de casser la vraie base de données MySQL.

### 4. Programmation Événementielle
J'utilise le système d'événements de Symfony pour réagir à certaines actions sans modifier le cœur du code :
* **`LoginListener` :** Met à jour la date de dernière connexion quand un utilisateur se connecte.
* **`LoginAttemptSubscriber` :** Enregistre les logs de connexion (Succès/Échec).
* **`ApiExceptionListener` :** Centralise la gestion des erreurs de l'API.

### 5. Design & UI (Netflix Style)
L'interface a été personnalisée avec un thème sombre (Dark Mode) inspiré de Netflix :
* Couleurs : Noir profond (`#141414`) et Rouge (`#E50914`).
* Ergonomie : Inputs sombres adaptés pour ne pas éblouir, tableaux responsifs et icônes pour une meilleure lisibilité.

---

## 🛠️ Configuration

### Prérequis
* PHP 8.1 ou supérieur
* Symfony CLI
* Base de données : MySQL (pour le site) et le pilote SQLite (pour les tests).

### Installation

1.  **Cloner et installer les dépendances :**
    ```bash
    composer install
    ```

2.  **Base de données (Dev - MySQL) :**
    Créez un fichier `.env.local` avec vos identifiants MySQL, puis lancez :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

3.  **Lancer les Tests (Test - SQLite) :**
    Le projet utilise une base SQLite temporaire qui se crée toute seule.
    *Note : Assurez-vous d'avoir le driver `php-sqlite3` installé.*
    ```bash
    php bin/phpunit
    ```

### Accès Rapides
* **Site Web :** `http://localhost:8000`
* **Documentation API (Swagger) :** `http://localhost:8000/api/doc`
* **Admin Livres (AJAX) :** `http://localhost:8000/admin/books` (Nécessite le rôle ADMIN)

---

## 📝 Auteur
Projet réalisé par Yoann GOUMARRE dans le cadre du module Symfony.