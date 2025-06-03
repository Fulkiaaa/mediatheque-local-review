<?php
require 'auth.php';

$tables = ['films', 'acteurs', 'genres', 'films_acteurs', 'realisateurs', 'support', 'avis', 'version'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Films</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>🎬 Admin - Tables</h1>
    <div>
        <span class="me-3">Connecté : <strong><?= htmlspecialchars($_SESSION['nom_complet'] ?? $_SESSION['nom_utilisateur']) ?></strong></span>
        <a href="gestion_utilisateurs.php" class="btn btn-info me-2">👥 Utilisateurs</a>
        <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
    </div>
</div>

<div class="list-group">
  <?php foreach ($tables as $table): ?>
    <a href="table.php?table=<?= $table ?>" class="list-group-item list-group-item-action">
      <?= ucfirst($table) ?>
    </a>
  <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>