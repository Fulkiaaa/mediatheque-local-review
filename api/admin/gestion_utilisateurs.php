<?php
require 'auth.php';
require 'db.php';

$message = '';
$erreur = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'ajouter') {
        $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        
        if (empty($nom_utilisateur) || empty($email) || empty($mot_de_passe)) {
            $erreur = 'Tous les champs obligatoires doivent être remplis.';
        } else {
            try {
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, nom, prenom, role) VALUES (?, ?, ?, ?, ?, 'admin')");
                $stmt->execute([$nom_utilisateur, $email, $mot_de_passe_hash, $nom, $prenom]);
                $message = 'Utilisateur ajouté avec succès.';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $erreur = 'Ce nom d\'utilisateur ou email existe déjà.';
                } else {
                    $erreur = 'Erreur lors de l\'ajout de l\'utilisateur.';
                }
            }
        }
    } elseif ($action === 'desactiver') {
        $id = $_POST['id'] ?? 0;
        if ($id != $_SESSION['utilisateur_id']) { // Ne pas se désactiver soi-même
            $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = 0 WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Utilisateur désactivé.';
        } else {
            $erreur = 'Vous ne pouvez pas vous désactiver vous-même.';
        }
    } elseif ($action === 'activer') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Utilisateur activé.';
    }
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT id, nom_utilisateur, email, nom, prenom, actif, date_creation, derniere_connexion FROM utilisateurs ORDER BY date_creation DESC");
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Admin Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 Gestion des utilisateurs</h1>
        <div>
            <span class="me-3">Connecté : <strong><?= htmlspecialchars($_SESSION['nom_complet'] ?? $_SESSION['nom_utilisateur']) ?></strong></span>
            <a href="index.php" class="btn btn-secondary me-2">Retour</a>
            <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Ajouter un utilisateur</h5>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="col-md-6">
                    <label for="nom_utilisateur" class="form-label">Nom d'utilisateur *</label>
                    <input type="text" class="form-control" name="nom_utilisateur" required>
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="col-md-4">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" name="prenom">
                </div>
                
                <div class="col-md-4">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" name="nom">
                </div>
                
                <div class="col-md-4">
                    <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                    <input type="password" class="form-control" name="mot_de_passe" required>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Ajouter l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h5>Liste des utilisateurs administrateurs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Nom complet</th>
                            <th>Statut</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><strong><?= htmlspecialchars($user['nom_utilisateur']) ?></strong></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['actif'] ? 'success' : 'secondary' ?>">
                                        <?= $user['actif'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $user['derniere_connexion'] ? date('d/m/Y H:i', strtotime($user['derniere_connexion'])) : 'Jamais' ?>
                                </td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['utilisateur_id']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <?php if ($user['actif']): ?>
                                                <input type="hidden" name="action" value="desactiver">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                                    Désactiver
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="activer">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Activer
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php else: ?>
                                        <em class="text-muted">Vous</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>