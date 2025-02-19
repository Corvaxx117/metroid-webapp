# Mini Framework PHP MVC

Ce projet est une application PHP développée dans le cadre de la formation OpenClassRoom. Il s'agit d'un mini-framework MVC (Model-View-Controller) inspiré de Symfony, conçu pour mieux comprendre la programmation orientée objet (POO) et les principes d'architecture MVC, ainsi que Symfony.

---

## 🚀 Fonctionnalités

- Implémentation du pattern MVC.
- Système de routage basé sur des fichiers YAML.
- Chargement des configurations via des fichiers `.env` et `.envlocal`.
- Contrôleurs pour gérer les requêtes GET/POST.
- Prise en charge de la gestion des routes dynamiques (ex. `/news/:id`).
- Autoloader PSR-4 avec Composer.

---

## 🛠️ Installation et configuration

### Prérequis

- PHP >= 7.4 (recommandé : PHP 8.x)
- Serveur Apache avec `mod_rewrite` activé.
- Composer (gestionnaire de dépendances PHP).
- Base de données MySQL.

### Étapes d'installation

1. Clonez le dépôt :

   ```bash
   git clone https://github.com/USERNAME/nom-du-projet.git
   cd nom-du-projet
   composer install

   ```

### Informations relatives au MVC

- Modèle : cette partie gère ce qu'on appelle la logique métier du site. Elle comprend notamment la gestion des données qui sont stockées, mais aussi tout le code qui prend des décisions autour de ces données. Son objectif est de fournir une interface d'action la plus simple possible au contrôleur. On y trouve donc entre autres des algorithmes complexes et des requêtes SQL.

- Vue : cette partie se concentre sur l'affichage. Elle ne fait presque aucun calcul et se contente de récupérer des variables pour savoir ce qu'elle doit afficher. On y trouve essentiellement du code HTML mais aussi quelques boucles et conditions PHP très simples, pour afficher par exemple une liste de messages, d'articles, de produits etc ...

- Contrôleur : cette partie gère les échanges avec l'utilisateur. C'est en quelque sorte l'intermédiaire entre l'utilisateur, le modèle et la vue. Le contrôleur va recevoir des requêtes de l'utilisateur. Pour chacune, il va demander au modèle d'effectuer certaines actions (lire des articles de blog depuis une base de données, supprimer un commentaire) et de lui renvoyer les résultats (la liste des articles, si la suppression est réussie). Puis il va adapter ce résultat et le donner à la vue. Enfin, il va renvoyer la nouvelle page HTML, générée par la vue, à l'utilisateur.
