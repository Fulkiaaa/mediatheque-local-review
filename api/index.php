<?php
// Configuration
$version_file = 'version_local.txt';
$data_file = 'donnees_cachees.json';
$avis_file = 'avis_local.json';

// Gestion des avis locaux
function chargerAvis() {
    global $avis_file;
    if (file_exists($avis_file)) {
        return json_decode(file_get_contents($avis_file), true) ?? [];
    }
    return [];
}

function sauvegarderAvis($avis) {
    global $avis_file;
    file_put_contents($avis_file, json_encode($avis, JSON_PRETTY_PRINT));
}

// Traitement de l'ajout d'avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_avis') {
    $film_id = intval($_POST['film_id']);
    $vu = isset($_POST['vu']) ? 1 : 0;
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire'] ?? '');
    
    $avis_locaux = chargerAvis();
    $avis_locaux[$film_id] = [
        'vu' => $vu,
        'note' => $note,
        'commentaire' => $commentaire,
        'date' => date('Y-m-d H:i:s')
    ];
    
    sauvegarderAvis($avis_locaux);
    
    // Redirection pour éviter resoumission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit;
}

// Mise à jour des données depuis l'API
$version_locale = file_exists($version_file) ? trim(file_get_contents($version_file)) : '0';

$api_url = 'http://api-film/api.php';
$response = @file_get_contents($api_url);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['films'][0]['numero_version'])) {
        $version_distante = $data['films'][0]['numero_version'];
        
        if (version_compare($version_distante, $version_locale, '>')) {
            file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
            file_put_contents($version_file, $version_distante);
            $mise_a_jour = true;
        }
    }
}

// Chargement des données locales
$films = [];
if (file_exists($data_file)) {
    $data = json_decode(file_get_contents($data_file), true);
    $films = $data['films'] ?? [];
}

// Chargement des avis locaux
$avis_locaux = chargerAvis();

// Filtrage des films selon la recherche
$recherche = trim($_GET['recherche'] ?? '');
$films_filtres = $films;

if (!empty($recherche)) {
    $films_filtres = array_filter($films, function($film) use ($recherche) {
        return stripos($film['titre'], $recherche) !== false ||
               stripos($film['genre'], $recherche) !== false ||
               stripos($film['realisateur_nom'], $recherche) !== false ||
               stripos($film['realisateur_prenom'], $recherche) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Collection de Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .film-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .film-card:hover {
            transform: translateY(-5px);
        }
        .star-rating {
            color: #ffc107;
        }
        .star-rating .fa-star {
            cursor: pointer;
        }
        .star-rating .fa-star:hover {
            color: #ff8c00;
        }
        .film-poster {
            height: 200px;
            background: linear-gradient(135deg,rgb(234, 125, 61) 0%,rgb(247, 188, 121) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🎬 Ma Collection</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin/">📊 Administration</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- En-tête avec informations -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>🎬 Ma Collection de Films</h1>
                <p class="text-muted">
                    <?= count($films) ?> film(s) dans votre collection
                    <?php if (isset($mise_a_jour)): ?>
                        <span class="badge bg-success ms-2">Mis à jour</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1">Version locale : <strong><?= htmlspecialchars($version_locale) ?></strong></p>
                <small class="text-muted">Dernière sync : <?= date('d/m/Y H:i') ?></small>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> Avis ajouté avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <form method="GET" class="d-flex">
                    <input type="text" name="recherche" class="form-control me-2" 
                           placeholder="Rechercher par titre, genre, réalisateur..." 
                           value="<?= htmlspecialchars($recherche) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($recherche)): ?>
                        <a href="?" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (!empty($recherche)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <?= count($films_filtres) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
            </div>
        <?php endif; ?>

        <!-- Liste des films -->
        <div class="row">
            <?php if (empty($films_filtres)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-film fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">Aucun film trouvé</h3>
                    <p class="text-muted">
                        <?= empty($recherche) ? 'Votre collection est vide.' : 'Aucun film ne correspond à votre recherche.' ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($films_filtres as $film): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card film-card shadow-sm">
                            <div class="film-poster">
                                <i class="fas fa-film"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($film['titre']) ?></h5>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?= $film['annee'] ?> • 
                                        <i class="fas fa-clock"></i> <?= $film['duree'] ?>min
                                    </small>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="badge bg-primary"><?= htmlspecialchars($film['genre']) ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($film['support_type']) ?></span>
                                </div>
                                
                                <p class="card-text">
                                    <strong>Réalisateur :</strong> 
                                    <?= htmlspecialchars($film['realisateur_prenom'] . ' ' . $film['realisateur_nom']) ?>
                                </p>
                                
                                <?php if (!empty($film['acteurs'])): ?>
                                    <p class="card-text">
                                        <strong>Acteurs :</strong> 
                                        <?php 
                                        $acteurs_noms = array_map(function($acteur) {
                                            return $acteur['prenom'] . ' ' . $acteur['nom'];
                                        }, $film['acteurs']);
                                        echo htmlspecialchars(implode(', ', $acteurs_noms));
                                        ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars(substr($film['synopsis'], 0, 100)) ?>...
                                </p>
                                
                                <!-- Avis existant -->
                                <?php if (isset($avis_locaux[$film['film_id']])): ?>
                                    <?php $avis = $avis_locaux[$film['film_id']]; ?>
                                    <div class="alert alert-success p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <?php if ($avis['vu']): ?>
                                                    <i class="fas fa-eye text-success"></i> Vu
                                                <?php else: ?>
                                                    <i class="fas fa-eye-slash text-muted"></i> À voir
                                                <?php endif; ?>
                                            </span>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= $avis['note'] ? '' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($avis['commentaire'])): ?>
                                            <small class="d-block mt-1"><?= htmlspecialchars($avis['commentaire']) ?></small>
                                        <?php endif; ?>
                                        <small class="text-muted">Ajouté le <?= date('d/m/Y', strtotime($avis['date'])) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer">
                                <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" 
                                        data-bs-target="#avisModal<?= $film['film_id'] ?>">
                                    <i class="fas fa-star"></i> 
                                    <?= isset($avis_locaux[$film['film_id']]) ? 'Modifier mon avis' : 'Donner mon avis' ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour avis -->
                    <div class="modal fade" id="avisModal<?= $film['film_id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Avis sur "<?= htmlspecialchars($film['titre']) ?>"</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="ajouter_avis">
                                        <input type="hidden" name="film_id" value="<?= $film['film_id'] ?>">
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="vu" id="vu<?= $film['film_id'] ?>"
                                                       <?= isset($avis_locaux[$film['film_id']]) && $avis_locaux[$film['film_id']]['vu'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="vu<?= $film['film_id'] ?>">
                                                    J'ai vu ce film
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Note (1-5 étoiles)</label>
                                            <div class="star-rating" data-film="<?= $film['film_id'] ?>">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star" data-note="<?= $i ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <input type="hidden" name="note" id="note<?= $film['film_id'] ?>" 
                                                   value="<?= $avis_locaux[$film['film_id']]['note'] ?? 5 ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="commentaire<?= $film['film_id'] ?>" class="form-label">
                                                Commentaire (optionnel)
                                            </label>
                                            <textarea class="form-control" name="commentaire" 
                                                      id="commentaire<?= $film['film_id'] ?>" rows="3"
                                                      placeholder="Vos impressions sur ce film..."><?= isset($avis_locaux[$film['film_id']]) ? htmlspecialchars($avis_locaux[$film['film_id']]['commentaire']) : '' ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion des étoiles de notation
        document.querySelectorAll('.star-rating').forEach(function(rating) {
            const filmId = rating.dataset.film;
            if (!filmId) return;
            
            const stars = rating.querySelectorAll('.fa-star');
            const noteInput = document.getElementById('note' + filmId);
            
            // Initialiser l'affichage selon la note existante
            const currentNote = parseInt(noteInput.value) || 5;
            updateStars(stars, currentNote);
            
            stars.forEach(function(star, index) {
                star.addEventListener('click', function() {
                    const note = index + 1;
                    noteInput.value = note;
                    updateStars(stars, note);
                });
                
                star.addEventListener('mouseover', function() {
                    updateStars(stars, index + 1);
                });
            });
            
            rating.addEventListener('mouseleave', function() {
                updateStars(stars, parseInt(noteInput.value) || 5);
            });
        });
        
        function updateStars(stars, note) {
            stars.forEach(function(star, index) {
                if (index < note) {
                    star.classList.remove('text-muted');
                } else {
                    star.classList.add('text-muted');
                }
            });
        }
    </script>
</body>
</html>