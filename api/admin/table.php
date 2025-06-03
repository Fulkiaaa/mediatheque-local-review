<?php
require 'db.php';
require 'auth.php';

$table = $_GET['table'] ?? null;
if (!$table) {
    die("Erreur : aucun nom de table fourni.");
}

try {
    $stmt = $pdo->query("SELECT * FROM `$table` LIMIT 100");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération de la table : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Table <?= htmlspecialchars($table) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h1>📄 Table : <?= htmlspecialchars($table ?? 'Inconnue') ?></h1>
<a href="index.php" class="btn btn-secondary mb-3">Retour</a>
<a href="edit.php?table=<?= urlencode($table) ?>" class="btn btn-primary mb-3 ms-2">Ajouter</a>

<?php if (!empty($data)): ?>
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
      <tr>
        <?php foreach (array_keys($data[0]) as $col): ?>
          <th><?= htmlspecialchars($col) ?></th>
        <?php endforeach; ?>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($data as $row): ?>
        <tr>
          <?php foreach ($row as $value): ?>
            <td><?= htmlspecialchars($value ?? '') ?></td>
          <?php endforeach; ?>
          <td>
            <a href="edit.php?table=<?= urlencode($table) ?>&id=<?= $row['id'] ?? '' ?>" class="btn btn-sm btn-warning">✏️</a>
            <a href="edit.php?table=<?= urlencode($table) ?>&delete=<?= $row['id'] ?? '' ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">🗑️</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <p>Aucune donnée trouvée dans la table.</p>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
