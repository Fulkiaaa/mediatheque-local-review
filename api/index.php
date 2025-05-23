<?php
// Configuration de l'API
$api_url = 'http://api-film/api.php/'; // Changez le chemin selon votre structure

// Fonction pour récupérer les données de l'API
function getApiData() {
    global $api_url;
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    
    $data = @file_get_contents($api_url, false, $context);
    
    if ($data === false) {
        return ['error' => 'Impossible de récupérer les données de l\'API'];
    }
    
    return json_decode($data, true);
}

// Récupération des données
$apiData = getApiData();
$hasError = isset($apiData['error']);

// Si pas d'erreur, on organise les données
if (!$hasError) {
    $films = $apiData['films'] ?? [];
    $genres = $apiData['genres'] ?? [];
    $realisateurs = $apiData['realisateurs'] ?? [];
    $acteurs = $apiData['acteurs'] ?? [];
    $supports = $apiData['support'] ?? [];
    $filmActeurs = $apiData['film_acteur'] ?? [];
    $version = $apiData['version'] ?? null;
    
    // Créer des index pour les relations
    $genresById = array_column($genres, null, 'id');
    $realisateursById = array_column($realisateurs, null, 'id');
    $acteursById = array_column($acteurs, null, 'id');
    $supportsById = array_column($supports, null, 'id');
    
    // Grouper les acteurs par film
    $acteursByFilm = [];
    foreach ($filmActeurs as $fa) {
        $acteursByFilm[$fa['film_id']][] = $fa['acteur_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médiathèque - Gestion des Films</title>
    
    <!-- Bootstrap CSS via CDN (pas d'installation requise) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .film-card img {
            height: 300px;
            object-fit: cover;
        }
        
        .badge-genre {
            font-size: 0.8em;
        }
        
        .actor-list {
            font-size: 0.9em;
            color: #6c757d;
        }
        
        .version-info {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        
        .stats-card {
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .film-card {
            transition: box-shadow 0.3s ease;
        }
        
        .film-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-film me-2"></i>
                Médiathèque
            </a>
            
            <div class="navbar-nav ms-auto">
                <?php if ($version): ?>
                    <span class="navbar-text version-info px-3 py-2 rounded">
                        Version <?php echo htmlspecialchars($version['numero'] ?? 'N/A'); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <?php if ($hasError): ?>
            <!-- Affichage d'erreur -->
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur de connexion
                </h4>
                <p><?php echo htmlspecialchars($apiData['error']); ?></p>
                <hr>
                <p class="mb-0">
                    <small>Vérifiez que votre serveur WAMP est démarré et que l'API est accessible à l'adresse : 
                    <code><?php echo htmlspecialchars($api_url); ?></code></small>
                </p>
            </div>
            
        <?php else: ?>
            
            <!-- Statistiques -->
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-film fa-2x mb-2"></i>
                            <h3><?php echo count($films); ?></h3>
                            <p class="mb-0">Films</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3><?php echo count($acteurs); ?></h3>
                            <p class="mb-0">Acteurs</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-2x mb-2"></i>
                            <h3><?php echo count($realisateurs); ?></h3>
                            <p class="mb-0">Réalisateurs</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-tags fa-2x mb-2"></i>
                            <h3><?php echo count($genres); ?></h3>
                            <p class="mb-0">Genres</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Titre du film...">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Genre</label>
                            <select id="genreFilter" class="form-select">
                                <option value="">Tous les genres</option>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo $genre['id']; ?>">
                                        <?php echo htmlspecialchars($genre['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Support</label>
                            <select id="supportFilter" class="form-select">
                                <option value="">Tous les supports</option>
                                <?php foreach ($supports as $support): ?>
                                    <option value="<?php echo $support['id']; ?>">
                                        <?php echo htmlspecialchars($support['type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des films -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-video me-2"></i>
                    Catalogue des Films
                </h2>
                <span class="badge bg-secondary" id="filmCount"><?php echo count($films); ?> films</span>
            </div>

            <div class="row" id="filmsContainer">
                <?php foreach ($films as $film): ?>
                    <div class="col-lg-4 col-md-6 mb-4 film-item" 
                         data-genre="<?php echo $film['genre_id']; ?>" 
                         data-support="<?php echo $film['support_id']; ?>"
                         data-title="<?php echo strtolower($film['titre']); ?>">
                        
                        <div class="card film-card h-100">
                            <?php if (!empty($film['image'])): ?>
                                <img src="<?php echo htmlspecialchars($film['image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($film['titre']); ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                                    <i class="fas fa-film fa-4x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($film['titre']); ?></h5>
                                
                                <div class="mb-2">
                                    <?php if (isset($genresById[$film['genre_id']])): ?>
                                        <span class="badge bg-primary badge-genre">
                                            <?php echo htmlspecialchars($genresById[$film['genre_id']]['nom']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($supportsById[$film['support_id']])): ?>
                                        <span class="badge bg-info badge-genre ms-1">
                                            <?php echo htmlspecialchars($supportsById[$film['support_id']]['type']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($film['synopsis'])): ?>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars(substr($film['synopsis'], 0, 150)) . (strlen($film['synopsis']) > 150 ? '...' : ''); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="mt-auto">
                                    <?php if (isset($realisateursById[$film['realisateur_id']])): ?>
                                        <p class="mb-1">
                                            <strong>Réalisateur:</strong> 
                                            <?php echo htmlspecialchars($realisateursById[$film['realisateur_id']]['nom']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($acteursByFilm[$film['id']])): ?>
                                        <div class="actor-list">
                                            <strong>Acteurs:</strong>
                                            <?php 
                                            $acteurNames = [];
                                            foreach ($acteursByFilm[$film['id']] as $acteurId) {
                                                if (isset($acteursById[$acteurId])) {
                                                    $acteur = $acteursById[$acteurId];
                                                    $acteurNames[] = $acteur['nom'];
                                                }
                                            }
                                            echo htmlspecialchars(implode(', ', array_slice($acteurNames, 0, 3)));
                                            if (count($acteurNames) > 3) {
                                                echo ' <small>et ' . (count($acteurNames) - 3) . ' autre(s)</small>';
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($film['annee'])): ?>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo htmlspecialchars($film['annee']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Message si aucun film trouvé -->
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun film trouvé</h4>
                <p class="text-muted">Essayez de modifier vos critères de recherche</p>
            </div>
            
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">
                &copy; 2024 Médiathèque - Propulsé par PHP & Bootstrap
                <?php if ($version): ?>
                    | API Version <?php echo htmlspecialchars($version['numero'] ?? 'N/A'); ?>
                <?php endif; ?>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Système de filtrage en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const genreFilter = document.getElementById('genreFilter');
            const supportFilter = document.getElementById('supportFilter');
            const filmsContainer = document.getElementById('filmsContainer');
            const filmCount = document.getElementById('filmCount');
            const noResults = document.getElementById('noResults');
            
            function filterFilms() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedGenre = genreFilter.value;
                const selectedSupport = supportFilter.value;
                
                const filmItems = document.querySelectorAll('.film-item');
                let visibleCount = 0;
                
                filmItems.forEach(item => {
                    const title = item.dataset.title;
                    const genre = item.dataset.genre;
                    const support = item.dataset.support;
                    
                    const matchesSearch = title.includes(searchTerm);
                    const matchesGenre = !selectedGenre || genre === selectedGenre;
                    const matchesSupport = !selectedSupport || support === selectedSupport;
                    
                    if (matchesSearch && matchesGenre && matchesSupport) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Mise à jour du compteur
                filmCount.textContent = visibleCount + ' films';
                
                // Affichage du message "aucun résultat"
                if (visibleCount === 0) {
                    filmsContainer.style.display = 'none';
                    noResults.style.display = 'block';
                } else {
                    filmsContainer.style.display = 'flex';
                    noResults.style.display = 'none';
                }
            }
            
            // Événements de filtrage
            searchInput.addEventListener('input', filterFilms);
            genreFilter.addEventListener('change', filterFilms);
            supportFilter.addEventListener('change', filterFilms);
        });
    </script>
</body>
</html>