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

```bash
composer create-project corvaxx/metroid-webapp-skeleton nom-de-mon-projet \
  --repository='{"type":"vcs","url":"https://github.com/Corvaxx117/metroid-webapp-skeleton"}' \
  --stability=dev --prefer-dist
```

---

## 🗂️ Structure générée

mon-projet/
├── bin/
│ └── post-install.php
├── config/
│ ├── config.php
│ └── route.yaml
├── public/
│ └── index.php
├── src/
│ └── Controller/
│ └── HomeController.php  
├── templates/
│ ├── home.phtml
│ ├── about.phtml
│ └── layout.phtml
├── .env
├── composer.json
├── vendor/
└── └── framework metroid

```

```
