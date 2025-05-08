# 🚀 Metroid – Starter WebApp (corvaxx/metroid-webapp)

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

- Commande d'installation du projet
- Le projet est scindé en deux repository `metroid-webapp` (dossier vendor) et `metroid-webapp-skeleton` pour l'architecture de base.

```bash
composer create-project corvaxx/metroid-webapp-skeleton nom-de-mon-projet \
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
- Tu peux ajouter tes **services** dans `src/Services/`

---

## 🗂️ Structure du squelette générée

```text
mon-projet/
├── bin/
│   └── post-install.php
├── config/
│   ├── config.php
│   └── route.yaml
├── public/
│   └── index.php
├── src/
│   └── Controller/
│       └── HomeController.php
├── templates/
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
    ├── src/
    │   ├── Controller/
    │   ├── Database/
    │   ├── ErrorHandler/
    │   ├── Exceptions/
    │   ├── FlashMessage/
    │   ├── http/
    │   ├── Model/
    │   ├── Router/
    │   ├── Services/
    │   ├── View/
    │   └── Launcher.php
```
