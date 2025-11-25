# 🌟 **ZooApp** 🐾

![Status](https://img.shields.io/badge/Status-Terminé-success)
![Type](https://img.shields.io/badge/Type-Fullstack-blue)

ZooApp est une application web développée avec le framework Symfony, conçue pour présenter un zoo. Elle propose un système de gestion des utilisateurs et des rôles, offrant une expérience personnalisée pour chaque type d'utilisateur.

## **Fonctionnalités principales** ✨

**Présentation du zoo** :
Consultation des habitats et des animaux du zoo.
Navigation accessible aux utilisateurs non connectés avec un accès limité.

**Gestion des utilisateurs** :
Système d'inscription et de connexion sécurisé.

**Gestion des rôles avec 4 types d'utilisateurs** :

**Admin** : Accès complet pour gérer les utilisateurs, les avis et le contenu.

**Vétérinaire** : Accès à une page dédiée pour gérer les animaux et leurs soins.

**Employé** : Accès à une page spécifique pour gérer les tâches liées au zoo.

**User** : Consultation des habitats, publication d'avis, et navigation générale.

**Gestion des habitats et avis** :
Chaque habitat est présenté avec des détails, une galerie d'images, et une liste des animaux présents.
Les utilisateurs connectés peuvent laisser des avis sur les habitats.

**Système de contact** :
Utilisation de Firebase pour gérer les messages de contact en temps réel.

## **Prérequis** 🛠️

**PHP** : Version 8.1 ou supérieure.
**Composer** : Gestionnaire de dépendances PHP.
**Symfony CLI** : Pour gérer le serveur et les commandes.
**Base de données** : MySQL ou un autre système compatible avec Doctrine.
**Firebase** : Service utilisé pour gérer les messages de contact.

## **Installation** 🚀

### Étapes :

1. **Cloner le dépôt :**

#### Copier le code
```bash
git clone https://github.com/votre-repo/zooapp.git
cd zooapp
```

2. **Installer les dépendances :**

#### Copier le code
```bash
composer install
```

3. **Configurer l'application :**

Copier le fichier .env :

#### Copier le code
```bash
cp .env .env.local
```

4. **Modifier les variables de configuration dans .env.local :**

#### dotenv
Copier le code
DATABASE_URL="mysql://user:password@127.0.0.1:3306/zooapp"
FIREBASE_API_KEY="votre-clé-firebase"
FIREBASE_DATABASE_URL="https://votre-instance-firebase.firebaseio.com"

5. **Initialiser la base de données :**

#### Copier le code
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

6. **Lancer le serveur :**

#### Copier le code
```bash
symfony server:start
```

7. **Accéder à l'application :**

Rendez-vous sur http://127.0.0.1:8000.

## **Rôles et permissions** 🔐

**Rôle**	**Description**
**Admin**	Gestion complète : utilisateurs, habitats,  avis, et animaux.
**Vétérinaire**	Gestion des animaux et de leurs informations médicales.
**Employé**	Gestion des tâches opérationnelles du zoo.
**Utilisateur**	Consultation des habitats et des animaux, ajout d'avis sur les habitats.
**Visiteur**	Accès limité aux pages publiques sans authentification.

## **Structure de l'application** 📂

**Backend & Architecture :**
* **PHP / Symfony** (Architecture MVC robuste)
* **API Platform** (pour l'exposition des données)

**Base de données :**
* **MySQL** (Stockage relationnel)
* **Doctrine ORM** (Abstraction et gestion des entités)

**Frontend :**
* **Twig / Bootstrap** (Interface responsive)
* **JavaScript** (Interactions dynamiques)

**src/Entity** : Contient les entités principales (User, Habitat, Avis).

**src/Controller** : Contrôleurs pour gérer les routes et la logique métier.

**src/Service** : Services pour l'intégration avec Firebase et autres fonctionnalités.

**templates** : Fichiers Twig pour le rendu des pages.

**public** : Fichiers publics comme les assets CSS/JS.



## Licence 📄
Ce projet est sous licence MIT. Consultez le fichier LICENSE pour plus d'informations.

## 🚀 Installation Locale

 **Cloner le dépôt**
```bash
git clone [https://github.com/Nairod97425/Zoo-Projet.git](https://github.com/Nairod97425/Zoo-Projet.git)
cd Zoo-Projet
```

**Dépendances**
```bash
composer install
npm install
```

**Base de données**
* Configurer le `.env` avec vos accès BDD.
* Créer la base et les tables :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**Lancer le serveur**
```bash
symfony server:start
```

---

## 👤 Auteur
**Dorian Hoareau** - Développeur Web  
[Mon Portfolio](https://nairod97425.github.io/portfolio) | [LinkedIn](https://www.linkedin.com/in/dorian-hoareau-59aba2151/)
