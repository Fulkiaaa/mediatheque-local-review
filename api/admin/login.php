<?php
// login.php
session_start();
require 'db.php';

$erreur = '';

// Si déjà connecté, rediriger
if (isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        try {
            // Recherche de l'utilisateur
            $stmt = $pdo->prepare("SELECT id, nom_utilisateur, mot_de_passe, nom, prenom, actif FROM utilisateurs WHERE nom_utilisateur = ? AND actif = 1");
            $stmt->execute([$nom_utilisateur]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['utilisateur_id'] = $utilisateur['id'];
                $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
                $_SESSION['nom_complet'] = $utilisateur['prenom'] . ' ' . $utilisateur['nom'];
                
                // Mise à jour de la dernière connexion
                $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
                $stmtUpdate->execute([$utilisateur['id']]);
                
                header('Location: index.php');
                exit;
            } else {
                $erreur = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $erreur = 'Erreur de connexion à la base de données.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Admin Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(234, 125, 61) 0%,rgb(247, 188, 121) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">🎬 Admin Films</h2>
                        <p class="text-muted">Connexion à l'interface d'administration</p>
                    </div>
                    
                    <?php if ($erreur): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nom_utilisateur" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" 
                                   value="<?= htmlspecialchars($_POST['nom_utilisateur'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
                        </div>
                    </form>
                    
                    <!-- <div class="text-center">
                        <p class="mb-2">Pas encore de compte ?</p>
                        <a href="register.php" class="btn btn-outline-success">Créer un compte</a>
                    </div> -->
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Compte de test :</strong><br>
                            Admin : admin / admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>