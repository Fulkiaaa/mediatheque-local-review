<?php
// api.php
header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=films_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données
    $stmt = $pdo->query("
        SELECT 
            f.id AS film_id, f.titre, f.synopsis, f.annee, f.duree,
            g.nom AS genre,
            r.nom AS realisateur_nom, r.prenom AS realisateur_prenom,
            s.type AS support_type, s.numero_serie,
            v.numero_version, v.date_version
        FROM films f
        JOIN genres g ON f.id_genre = g.id
        JOIN realisateurs r ON f.id_realisateur = r.id
        JOIN support s ON f.id_support = s.id
        LEFT JOIN version v ON v.id = (SELECT MAX(id) FROM version)
    ");
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pour chaque film, récupérer les acteurs associés
    foreach ($films as &$film) {
        $film_id = $film['film_id'];
        $acteursStmt = $pdo->prepare("
            SELECT a.nom, a.prenom, a.date_naissance, a.date_deces
            FROM acteurs a
            JOIN films_acteurs fa ON a.id = fa.id_acteur
            WHERE fa.id_film = ?
        ");
        $acteursStmt->execute([$film_id]);
        $film['acteurs'] = $acteursStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['films' => $films], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'message' => $e->getMessage()]);
}
