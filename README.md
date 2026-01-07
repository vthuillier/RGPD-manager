# 🛡️ RGPD Manager

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.2-777bb4?style=flat-square&logo=php)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-336791?style=flat-square&logo=postgresql)](https://www.postgresql.org/)
[![Tailwind CSS](https://img.shields.io/badge/CSS-Tailwind-38b2ac?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)

**RGPD Manager** est une solution SaaS de pointe, conçue pour simplifier et automatiser la gestion de la conformité au Règlement Général sur la Protection des Données. Alliant design premium, sécurité maximale et ergonomie pensée pour les DPO, elle transforme la contrainte légale en un levier de gouvernance.

---

## ✨ Fonctionnalités Premium

L'application offre un écosystème complet pour le pilotage de la donnée :

- 📊 **Executive Dashboard** : Vue 360° en temps réel, alertes intelligentes sur les rétentions et priorisation des actions urgentes.
- 📝 **Registre des Traitements (Art. 30)** : Gestion granulaire des activités, bases légales, catégories de données et destinataires.
- 🎯 **Module AIPD (DPIA)** : Workflow complet d'Analyse d'Impact avec évaluation des risques et validation tripartite.
- 🌐 **Transferts Hors-UE (DTIA)** : Évaluation de l'impact des transferts internationaux (Data Transfer Impact Assessment).
- � **Indice de Maturité** : Auto-évaluation par piliers avec visualisation radar et suivi de progression temporelle.
- 📂 **Gestion des Droits & Violations** : Registres dédiés pour l'exercice des droits et la documentation des incidents (CNIL ready).
- 🤝 **Portail Sous-traitants** : Inventaire et suivi des engagements contractuels des partenaires tiers.
- � **Reporting de Haute Qualité** : Génération de fiches PDF professionnelles et du **Rapport Annuel de Conformité**.

---

## 🔒 Sécurité Sans Compromis

La sécurité n'est pas une option, c'est le cœur de l'architecture :

- **Architecture Multi-entités** : Isolation physique et logique stricte entre les organisations.
- **RBAC Avancé** : Gestion fine des permissions (Super Admin, Org Admin, DPO, Viewer).
- **Audit Trail** : Journalisation immuable de toutes les actions (Qui a fait quoi, quand ?).
- **Hardening Sécurité** : Content Security Policy (CSP) stricte, protection CSRF/XSS native, et mots de passe hashés avec Argon2id.
- **Privacy by Design** : Minimisation native de la collecte de données sur les utilisateurs de la plateforme.

---

## 🛠️ Excellence Technique

- **Backend** : Architecture PHP 8.2+ MVC, légère, performante et hautement maintenable.
- **Database** : PostgreSQL pour son intégrité transactionnelle et sa robustesse.
- **Frontend** : Crafté avec **Tailwind CSS**, incluant des effets de _Glassmorphism_ et une iconographie **Lucide Icons** pour une expérience fluide.
- **DevOps** : Pipeline CI/CD intégré, gestion automatique des migrations et versioning applicatif intelligent.

---

## 🚀 Mise en œuvre Rapide

### Prérequis

- PHP 8.2+ (ext-gd, pdo_pgsql, openssl, mbstring)
- PostgreSQL
- Serveur HTTP (Apache/Nginx) ou CLI

### Installation en 3 étapes

1. **Clonage & Config** :
   ```bash
   git clone https://github.com/vthuillier/rgpd-manager.git
   cp .env.example .env # Renseignez vos accès DB
   ```
2. **Setup Initial** :
   Importez le fichier `init.sql` dans votre base PostgreSQL.
3. **Lancement** :
   ```bash
   php -S localhost:8000 -t public
   ```
   Rendez-vous sur `http://localhost:8000` pour finaliser la configuration via l'assistant interactif.

---

## 🤝 L'Alliance Homme-Machine

Ce projet est le fruit d'une collaboration étroite entre **Valentin Thuillier** et **Antigravity**, l'agent de codage avancé de **Google DeepMind**.

L'usage de l'IA a permis d'atteindre un niveau de raffinement dans la logique métier et une vélocité de développement records, tout en garantissant des standards de qualité de code (linting, tests) dignes des meilleures productions industrielles.

---

## 📜 Licence

Projet distribué sous licence **MIT**. Développé avec passion pour une protection des données plus transparente.
