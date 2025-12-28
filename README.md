# 🛡️ RGPD Manager

[![GitLab CI](https://img.shields.io/badge/CI%2FCD-GitLab-orange?style=flat-square&logo=gitlab)](https://gitlab.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.2-777bb4?style=flat-square&logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-ready-2496ed?style=flat-square&logo=docker)](https://docker.com)

**RGPD Manager** est une application web moderne et intuitive conçue pour simplifier la mise en conformité au Règlement Général sur la Protection des Données (RGPD). Elle permet de centraliser le registre des traitements, de gérer les sous-traitants, de suivre les exercices de droits et de documenter les violations de données.

---

## ✨ Fonctionnalités clés

- 📊 **Tableau de bord intelligent** : Visualisation en temps réel de votre état de conformité.
- 📝 **Registre des traitements** : Gestion complète des activités (Art. 30).
- 🤝 **Gestion des sous-traitants** : Cartographie des flux et garanties.
- 📂 **Exercice des droits** : Suivi rigoureux des demandes (Accès, Oubli, etc.) avec alertes de délais.
- 🚨 **Registre des violations** : Documentation des incidents et aide à la notification (72h).
- 📈 **Reporting Stratégique** : Génération d'un rapport annuel complet en **PDF** avec logo personnalisé.
- 📱 **Interface Responsive** : Accessible sur PC, tablette et smartphone.

---

## 🚀 Installation & Démarrage

### Via Docker (Recommandé)

1. Clonez le dépôt.
2. Configurez votre fichier `.env` (voir `.env.example`).
3. Lancez les conteneurs :
   ```bash
   docker compose up -d
   ```
4. L'application est accessible sur `http://localhost:8080`.

### Installation manuelle

1. Installez les dépendances PHP via Composer :
   ```bash
   composer install
   ```
2. Assurez-vous d'avoir une base de données **PostgreSQL** active.
3. Configurez les accès dans `config.php` ou via les variables d'environnement.
4. Activez l'extension PHP **GD** pour la génération des rapports PDF avec logos.
5. Lancez le serveur :
   ```bash
   php -S localhost:8000 -t public
   ```

---

## 🛠️ Stack Technique

- **Backend** : PHP 8.2+ (Architecture MVC légère)
- **Frontend** : Tailwind CSS, Vanilla JS
- **Base de données** : PostgreSQL
- **PDF Engine** : Dompdf
- **DevOps** : Docker, CI/CD GitLab, Docker-in-Docker (Build & Lint)

---

## 📜 Licence

Ce projet est sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🤝 Crédits

Développé par **Valentin Thuillier** ([valentin-thuillier.fr](https://valentin-thuillier.fr))
Propulsé par **Antigravity de Google**, technologie IA de pointe pour le codage agentique.
