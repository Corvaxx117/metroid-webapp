# 🚀 Metroid – Starter WebApp

Un mini-framework PHP MVC moderne, léger et typé, inspiré de Symfony.  
Idéal pour démarrer rapidement un projet web structuré sans toute la complexité d’un gros framework.

---

## ✨ Fonctionnalités principales

- 🔁 Architecture MVC propre et modulaire
- ⚙️ Routage via YAML (`route.yaml`)
- 🧠 Contrôleurs typés avec autoloading PSR-4
- 🪶 Objets `Request` et `Response` pour centraliser les flux HTTP
- 🧱 Système de vues avec layout partagé
- 🧩 Injection simple des services (`ViewRenderer`, `FlashMessage`, etc.)
- 🧪 Structure prête pour les tests
- ✅ Gestion d'erreurs avec vue personnalisée

---

## 📦 Installation

La structure est divisée en **2 dépôts distincts** :

1. [`metroid-webapp`](https://github.com/Corvaxx117/metroid-webapp) → Le cœur du framework (installé via Composer dans `/vendor`)
2. [`metroid-webapp-skeleton`](https://github.com/Corvaxx117/metroid-webapp-skeleton) → Le squelette de projet à la racine

## 🧮 Commande d'installation

Une seule commande permet d'installer les deux dépôts

```bash
composer create-project corvaxx/metroid-webapp-skeleton mon-projet \
  --repository='{"type":"vcs","url":"https://github.com/Corvaxx117/metroid-webapp-skeleton"}' \
  --stability=dev --prefer-dist
```

## 🔧 Configuration de base

- Le fichier `.env` est généré automatiquement depuis `.env.example`
- Configure votre base de données et options d’environnement dans ce fichier
- Les routes sont définies dans `config/route.yaml` avec la syntaxe :

```yaml
routes:
  /home:
    method: GET
    callable: App\Controller\HomeController::index
```

---

## 📁 Où développer ?

- Les **contrôleurs** vont dans `src/Controller/`
- Les **modèles** dans `src/Model/`
- Les **vues** dans `templates/`
- Ajouter vos **services** dans `src/Services/`

---

## 🗂️ Structure du squelette générée

```text
mon-projet/
├── assets/
│   ├── CSS/
│   │   ├── flashMessage.css
│   │   ├── style.css
│   │   └── system-errors.css
│   └── JS/
│       └── main.js
├── bin/
│   └── post-install.php
├── config/
│   ├── config.php
│   └── route.yaml
├── public/
│   └── index.php
├── src/
│   └── Controller/
│   │   ├── HomeController.php
│   │   └── AboutController.php
│   └── Model/
├── templates/
│   ├── about.phtml
│   ├── flashMessage.phtml
│   ├── layout.phtml
│   ├── home.phtml
│   └── about.phtml
├── .env
├── composer.json
├── vendor/
│   └── corvaxx/
│       └── metroid-webapp/
```

---

## 🗂️ Structure du framework Metroid générée (dossier vendor)

```text
corvaxx/
  └── metroid-webapp/
    ├── bin/
    │   └── installer.php
    └── src/
        ├── Controller/
        │   └── AbstractController.php
        ├── Database/
        │   ├── Model/
        │   |    └── TableAbstractModel.php
        │   └── Connection.php
        ├── ErrorHandler/
        │   └── ErrorHandler.php
        ├── Exceptions/
        │   ├── BadRequestException.php
        │   ├── InternalServerErrorException.php
        │   ├── NotFoundException.php
        │   └── ...
        ├── FlashMessage/
        │   ├── Handler/
        │   |    ├── NoSessionHandler.php
        │   |    └── SessionHandler.php
        │   └── FlashMessage.php
        ├── http/
        │   ├── Request.php
        │   └── Response.php
        ├── Model/
        │   └── ModelFactory.php
        ├── Router/
        │   └── Router.php
        ├── Services/
        │   ├── AuthService.php
        │   ├── FormatToFrenchDate.php
        │   ├── TextHandler.php
        │   └── UrlGenerator.php
        ├── View/
        │   └── ViewRenderer.php
        └── Launcher.php
```
