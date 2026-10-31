# Gestion de Bibliothèque

Une application web légère conçue pour **gérer les emprunts de livres dans une bibliothèque**. Ce projet permet de suivre les lecteurs, d'enregistrer les formulaires d'inscription et de gérer l'historique des emprunts.

## 🚀 Fonctionnalités
- **Gestion des Lecteurs** : Inscription et liste complète des utilisateurs.
- **Gestion des Emprunts** : Enregistrement des flux de livres et suivi des retours.
- **Base de données** : Structure relationnelle SQL optimisée pour l'application.

## 📁 Structure du Projet
Le projet est organisé de la manière suivante :
- `Gestion_bibiothecque/`
  - `.env/` : Fichiers de configuration et script SQL (`gestionbibiotheque.sqp.sql`).
  - `config/` : Connexion à la base de données (`database.php`).
  - `forms.html/` : Formulaires HTML pour les lecteurs et les emprunts.
  - `logique/` : Scripts PHP gérant le traitement des formulaires.
  - `style.css/` : Feuilles de style pour chaque interface de l'application.

## 🛠️ Installation et Configuration locales

### Prérequis
- **XAMPP** (avec PHP 8.x et MySQL)
- **Git**

### Installation
1. Clonez le projet ou placez-le dans votre dossier `xampp/htdocs/`.
2. Démarrez les modules **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.
3. Importez le fichier de base de données situé dans `Gestion_bibiothecque/.env/gestionbibiotheque.sqp.sql` via **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Configurez vos accès à la base de données dans le fichier `Gestion_bibiothecque/config/database.php`.
5. Accédez à l'application via votre navigateur : `http://localhost/dev/gestionBibiotheque/Gestion_bibiothecque/`

## 👥 Auteur
- **weteFranck**
