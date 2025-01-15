# Mini Framework PHP MVC

Ce projet est une application PHP développée dans le cadre de la formation OCR. Il s'agit d'un mini-framework MVC (Model-View-Controller) inspiré de Symfony, conçu pour mieux comprendre la programmation orientée objet (POO) et les principes d'architecture MVC.

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
