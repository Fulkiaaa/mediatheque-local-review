<?php
require 'db.php';

$table = $_GET['table'] ?? null;
$id = $_GET['id'] ?? null;
$delete = $_GET['delete'] ?? null;

// Libellés personnalisés
$labels = [
    'id_genre' => 'Genre',
    'id_realisateur' => 'Réalisateur',
    'id_support' => 'Support',
    'id_film' => 'Film',
    'id_acteur' => 'Acteur',
    'titre' => 'Titre',
    'synopsis' => 'Synopsis',
    'annee' => 'Année',
    'duree' => 'Durée en minutes',
    'date_naissance' => 'Date de naissance',
    'date_deces' => 'Date de décès',
    'numero_serie' => 'Numéro de série',
];

if (!$table) {
    die("Erreur : nom de table manquant.");
}

// Suppression
if ($delete) {
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->execute([$delete]);
    // Incrémentation de la version (mise à jour de la ligne id=1)
    $pdo->prepare("UPDATE version SET numero_version = numero_version + 1, date_version = NOW() WHERE id = 1")->execute();
    header("Location: table.php?table=" . urlencode($table));
    exit;
}

// Récupère colonnes de la table
try {
    $stmtCols = $pdo->query("DESCRIBE `$table`");
    $cols = $stmtCols->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des colonnes : " . $e->getMessage());
}

// Données existantes si modification
$values = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    $values = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $params = [];
    foreach ($cols as $col) {
        if ($col === 'id') continue;
        $data[] = $col . ' = ?';
        $params[] = $_POST[$col] ?? null;
    }

    if ($id) {
        $params[] = $id;
        $sql = "UPDATE `$table` SET " . implode(', ', $data) . " WHERE id = ?";
    } else {
        $sql = "INSERT INTO `$table` SET " . implode(', ', $data);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Incrémentation de la version
    $stmtVersion = $pdo->query("SELECT IFNULL(MAX(numero_version),0)+1 AS next_version FROM version");
    $nextVersion = $stmtVersion->fetchColumn();
    $pdo->prepare("UPDATE version SET numero_version = numero_version + 1, date_version = NOW() WHERE id = 1")->execute();

    header("Location: table.php?table=" . urlencode($table));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= $id ? 'Modifier' : 'Ajouter' ?> - <?= htmlspecialchars($table) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h1><?= $id ? '✏️ Modifier' : '➕ Ajouter' ?> dans <strong><?= htmlspecialchars($table) ?></strong></h1>

<form method="post" class="mt-4">
    
  <?php foreach ($cols as $col): ?>
  <?php if ($col === 'id') continue; ?>
  <div class="mb-3">
    <label for="<?= $col ?>" class="form-label"><?= $labels[$col] ?? ucfirst($col) ?></label>
    <?php
    // Liste déroulante pour les champs liés
        if ($col === 'id_genre') {
            $stmt = $pdo->query("SELECT id, nom FROM genres");
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled >-- Sélectionner un genre --</option>';
            foreach ($options as $option) {
                $selected = ($option['id'] == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='{$option['id']}' $selected>{$option['nom']}</option>";
            }
            echo '</select>';
        } elseif ($col === 'id_realisateur') {
            $stmt = $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) AS nom_complet FROM realisateurs");
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled >-- Sélectionner un réalisateur --</option>';
            foreach ($options as $option) {
                $selected = ($option['id'] == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='{$option['id']}' $selected>{$option['nom_complet']}</option>";
            }
            echo '</select>';
        } elseif ($col === 'id_support') {
            $stmt = $pdo->query("SELECT id, type FROM support");
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled >-- Sélectionner un support --</option>';
            foreach ($options as $option) {
                $selected = ($option['id'] == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='{$option['id']}' $selected>{$option['type']}</option>";
            }
            echo '</select>';
        } elseif ($col === 'id_film') {
            $stmt = $pdo->query("SELECT id, titre FROM films");
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled selected hidden>-- Sélectionner un film --</option>';
            foreach ($options as $option) {
                $selected = ($option['id'] == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='{$option['id']}' $selected>{$option['titre']}</option>";
            }
            echo '</select>';

        } elseif ($col === 'id_acteur') {
            $stmt = $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) AS nom_complet FROM acteurs");
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled selected hidden>-- Sélectionner un acteur --</option>';
            foreach ($options as $option) {
                $selected = ($option['id'] == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='{$option['id']}' $selected>{$option['nom_complet']}</option>";
            }
            echo '</select>';
        } elseif ($col === 'duree') {
            echo '<input type="number" class="form-control" name="' . $col . '" id="' . $col . '" value="' . htmlspecialchars($values[$col] ?? '') . '" min="0" step="1">';
        } elseif ($col === 'annee') {
            $currentYear = date('Y');
            $startYear = 1900;
            echo '<select class="form-select" name="' . $col . '" id="' . $col . '">';
            echo '<option value="" disabled >-- Sélectionner une année --</option>';
            for ($y = $currentYear; $y >= $startYear; $y--) {
                $selected = ($y == ($values[$col] ?? '')) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            echo '</select>';
        }
        elseif ($col === 'date_naissance' || $col === 'date_deces') {
            echo '<input type="date" class="form-control" name="' . $col . '" id="' . $col . '" value="' . htmlspecialchars($values[$col] ?? '') . '">';
        }
        else {
            // Champ texte par défaut
            echo '<input type="text" class="form-control" name="' . $col . '" id="' . $col . '" value="' . htmlspecialchars($values[$col] ?? '') . '">';
        }
        ?>
    </div>
    <?php endforeach; ?>

  <button type="submit" class="btn btn-success">💾 Enregistrer</button>
  <a href="table.php?table=<?= urlencode($table) ?>" class="btn btn-secondary">Retour</a>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
