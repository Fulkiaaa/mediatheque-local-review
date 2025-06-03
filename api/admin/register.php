<?php
// register.php - Page d'inscription
session_start();
require 'db.php';

$message = '';
$erreur = '';

// Si déjà connecté, rediriger
if (isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    
    // Validation
    if (empty($nom_utilisateur) || empty($email) || empty($mot_de_passe)) {
        $erreur = 'Tous les champs obligatoires doivent être remplis.';
    } elseif ($mot_de_passe !== $confirmer_mot_de_passe) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($mot_de_passe) < 6) {
        $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'L\'adresse email n\'est pas valide.';
    } else {
        try {
            // Vérifier si l'utilisateur ou l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE nom_utilisateur = ? OR email = ?");
            $stmt->execute([$nom_utilisateur, $email]);
            
            if ($stmt->fetchColumn() > 0) {
                $erreur = 'Ce nom d\'utilisateur ou cette adresse email est déjà utilisé.';
            } else {
                // Créer le compte
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, nom, prenom, role) VALUES (?, ?, ?, ?, ?, 'admin')");
                $stmt->execute([$nom_utilisateur, $email, $mot_de_passe_hash, $nom, $prenom]);
                
                $message = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            }
        } catch (PDOException $e) {
            $erreur = 'Erreur lors de la création du compte.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Admin Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .register-card {
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
            <div class="col-md-6 col-lg-5">
                <div class="register-card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">🎬 Admin Films</h2>
                        <p class="text-muted">Créer un compte administrateur</p>
                    </div>
                    
                    <?php if ($erreur): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nom_utilisateur" class="form-label">Nom d'utilisateur *</label>
                            <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" 
                                   value="<?= htmlspecialchars($_POST['nom_utilisateur'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe * (min. 6 caractères)</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Créer le compte</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Déjà un compte ?</p>
                        <a href="login.php" class="btn btn-outline-secondary">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>