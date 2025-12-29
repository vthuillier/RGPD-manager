# 🛡️ RGPD Manager

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.2-777bb4?style=flat-square&logo=php)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-336791?style=flat-square&logo=postgresql)](https://www.postgresql.org/)
[![Tailwind CSS](https://img.shields.io/badge/CSS-Tailwind-38b2ac?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)

**RGPD Manager** est une solution complète, moderne et sécurisée pour le pilotage de la conformité RGPD. Conçue pour les DPO et les organisations soucieuses de la protection des données, elle offre une interface intuitive pour centraliser et automatiser les obligations légales.

---

## ✨ Fonctionnalités clés

L'application couvre l'intégralité des besoins opérationnels d'un DPO :

- 📊 **Tableau de bord de pilotage** : Vue d'ensemble des traitements, alertes sur les délais de rétention, rappels d'AIPD et urgences (droits/violations).
- 📝 **Registre des traitements (Art. 30)** : Gestion exhaustive des activités de traitement avec catégorisation des données et bases légales.
- 🎯 **Module AIPD (DPIA)** : Réalisation d'Analyses d'Impact, évaluation des risques pour les droits et libertés, et workflow de validation (DPO/Responsable).
- 🤝 **Gestion des sous-traitants** : Inventaire des services tiers et partenaires manipulant des données personnelles.
- 📂 **Exercice des droits** : Gestion centralisée des demandes (Accès, Rectification, Suppression, etc.) avec suivi strict des délais.
- 🚨 **Registre des violations** : Documentation des incidents de sécurité et aide à la notification de la CNIL (délai de 72h).
- 📈 **Reporting & PDF** : Génération de fiches individuelles et d'un **Rapport Annuel de Conformité** consolidé, prêt pour la direction.

---

## 🔒 Sécurité & Privacy by Design

Le projet a été refondu avec une exigence de sécurité maximale :

- **Multi-tenancy (Multi-organisations)** : Isolation stricte des données entre les différentes organisations.
- **Contrôle d'accès (RBAC)** : Rôles hiérarchisés (Super Admin, Organisme Admin, Utilisateur, Guest).
- **Hardening HTTP** : Politique de sécurité du contenu (CSP) stricte, protection XSS, CSRF et injection SQL.
- **Audit Logs** : Journalisation complète de toutes les actions sensibles (création, modification, suppression).
- **Session Security** : Cookies sécurisés (HttpOnly, SameSite, Secure).

---

## 🛠️ Stack Technique

- **Langage** : PHP 8.2+ (Architecture MVC modulaire et légère)
- **Base de données** : PostgreSQL (pour la robustesse des données et les transactions)
- **Style** : Tailwind CSS (Design premium, responsive et mode sombre prêt)
- **Génération PDF** : Dompdf
- **Outils de Qualité** : ESLint, Prettier (Frontend), PHP CodeSniffer (Backend)

---

## 🚀 Installation

### Prérequis

- PHP 8.2 ou supérieur
- PostgreSQL
- Extension PHP `gd`, `pdo_pgsql`, `openssl`

### Mise en place rapide

1. **Clonez le projet**
2. **Initialisation de la base** : Importez le schéma situé dans `init.sql`.
3. **Configuration** : Copiez le fichier `.env.example` en `.env` et renseignez vos accès base de données.
4. **Premier démarrage** :
   ```bash
   # Création du premier compte admin (page setup au premier accès)
   php -S localhost:8000 -t public
   ```
5. Accédez à `http://localhost:8000` pour finaliser l'installation via l'assistant.

---

## 🤝 Crédits

Développé par **Valentin Thuillier** ([valentin-thuillier.fr](https://valentin-thuillier.fr)).

🚀 Ce projet a été développé en collaboration avec **Antigravity**, l'agent IA de codage de pointe conçu par **Google DeepMind**. L'utilisation de cette technologie a permis d'implémenter des logiques métier complexes et des standards de sécurité élevés en un temps record.

---

## 📜 Licence

Ce projet est sous licence **MIT**. Vous êtes libre de l'utiliser, de le modifier et de le distribuer.
