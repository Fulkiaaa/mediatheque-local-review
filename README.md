# Documentation API - Gestion de Films

## Vue d'ensemble

Cette API permet la gestion d'une base de données de films avec système d'authentification. Elle comprend la gestion des films, acteurs, réalisateurs, genres, supports et avis.

## Structure de la base de données

### Tables principales
- **films** : Informations sur les films
- **acteurs** : Données des acteurs
- **realisateurs** : Informations des réalisateurs
- **genres** : Catégories de films
- **support** : Types de support (DVD, Blu-ray, etc.)
- **films_acteurs** : Relation many-to-many films/acteurs
- **avis** : Commentaires et notes des utilisateurs
- **version** : Versioning des données
- **utilisateurs** : Comptes administrateurs

## Endpoints API

### 1. Récupération des données complètes

#### `GET /api.php`

Récupère tous les films avec leurs données associées (acteurs, réalisateurs, genres, supports).

**Réponse :**
```json
{
  "films": [
    {
      "film_id": 1,
      "titre": "Nom du film",
      "synopsis": "Description du film",
      "annee": 2020,
      "duree": 150,
      "genre": "Action",
      "realisateur_nom": "Nolan",
      "realisateur_prenom": "Christopher",
      "support_type": "Blu-ray",
      "numero_serie": "BR123456",
      "numero_version": 20,
      "date_version": "2025-06-04",
      "acteurs": [
        {
          "nom": "Washington",
          "prenom": "Denzel",
          "date_naissance": "1954-12-28",
          "date_deces": null
        }
      ]
    }
  ]
}
```

**Codes de réponse :**
- `200` : Succès
- `500` : Erreur de base de données

---

## Interface d'administration

### Authentification

#### `POST /login.php`

Connexion d'un utilisateur administrateur.

**Paramètres :**
```json
{
  "nom_utilisateur": "admin",
  "mot_de_passe": "password"
}
```

**Réponse :**
- Redirection vers `/index.php` si succès
- Message d'erreur si échec

#### `GET /logout.php`

Déconnexion de l'utilisateur.

**Réponse :**
- Redirection vers `/login.php`

### Gestion des données

#### `GET /table.php?table={nom_table}`

Affiche le contenu d'une table spécifique.

**Paramètres :**
- `table` : Nom de la table (films, acteurs, genres, etc.)

#### `GET /edit.php?table={nom_table}&id={id}`

Formulaire de modification d'un enregistrement.

**Paramètres :**
- `table` : Nom de la table
- `id` : ID de l'enregistrement (optionnel pour création)

#### `POST /edit.php`

Création ou modification d'un enregistrement.

**Paramètres :** Variables selon la table modifiée

#### `GET /edit.php?table={nom_table}&delete={id}`

Suppression d'un enregistrement.

**Paramètres :**
- `table` : Nom de la table
- `delete` : ID de l'enregistrement à supprimer

### Gestion des utilisateurs

#### `GET /gestion_utilisateurs.php`

Interface de gestion des utilisateurs administrateurs.

#### `POST /gestion_utilisateurs.php`

Actions sur les utilisateurs (création, activation/désactivation).

**Paramètres pour création :**
```json
{
  "action": "ajouter",
  "nom_utilisateur": "username",
  "email": "email@example.com",
  "mot_de_passe": "password",
  "nom": "Nom",
  "prenom": "Prénom"
}
```

**Paramètres pour activation/désactivation :**
```json
{
  "action": "activer|desactiver",
  "id": 1
}
```

---

## Fonctionnalités avancées

### Système de versioning

L'application utilise un système de versioning pour tracker les modifications :
- Chaque modification incrémente `numero_version` dans la table `version`
- La `date_version` est mise à jour automatiquement

### Gestion des relations

#### Films et Acteurs
Relation many-to-many via la table `films_acteurs` :
```sql
SELECT f.titre, a.nom, a.prenom 
FROM films f
JOIN films_acteurs fa ON f.id = fa.id_film
JOIN acteurs a ON fa.id_acteur = a.id
```

### Validation des données

#### Champs obligatoires par table :

**Films :**
- `titre` (texte)
- `annee` (année entre 1900 et année courante)
- `duree` (nombre entier positif)
- `id_genre` (référence)
- `id_realisateur` (référence)
- `id_support` (référence)

**Acteurs/Réalisateurs :**
- `nom` (texte)
- `prenom` (texte)
- `date_naissance` (date)
- `date_deces` (date, optionnel)

**Genres/Support :**
- `nom`/`type` (texte unique)

---

## Sécurité

### Authentification
- Sessions PHP sécurisées
- Hachage des mots de passe avec `password_hash()`
- Vérification des permissions sur chaque page admin

### Protection des données
- Échappement HTML avec `htmlspecialchars()`
- Requêtes préparées PDO contre l'injection SQL
- Validation côté serveur

### Gestion des erreurs
- Try-catch sur les opérations de base de données
- Messages d'erreur utilisateur-friendly
- Codes de statut HTTP appropriés

---

## Configuration

### Base de données
```php
$pdo = new PDO('mysql:host=localhost;dbname=films_db;charset=utf8', 'root', '');
```

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Extensions PHP : PDO, PDO_MySQL, session

---

## Exemples d'utilisation

### Récupération des films via API
```javascript
fetch('/api.php')
  .then(response => response.json())
  .then(data => {
    console.log(data.films);
  });
```

### Connexion administrateur
```html
<form method="POST" action="/login.php">
  <input type="text" name="nom_utilisateur" required>
  <input type="password" name="mot_de_passe" required>
  <button type="submit">Se connecter</button>
</form>
```

---

## Limitations et considérations

- **Authentification** : Basée sur les sessions PHP
- **Pagination** : Limitée à 100 résultats par table
- **Validation** : Principalement côté serveur
- **API** : Lecture seule pour les données publiques
- **Modification** : Uniquement via interface admin authentifiée

---

## Support et maintenance

### Logs d'erreurs
Les erreurs sont capturées et retournées au format JSON pour l'API.

### Sauvegarde
Recommandations :
- Sauvegarde régulière de la base de données
- Sauvegarde des fichiers de configuration
- Test de restauration périodique
