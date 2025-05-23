<?php declare(strict_types=1);

// Configuration de la base de données
$host       = '127.0.0.1';      // Adresse du serveur MySQL
$db         = 'api-film';        // Nom de la base de données
$user       = 'root';            // Nom d'utilisateur MySQL
$password   = '';                // Mot de passe MySQL (vide pour localhost)
$charset    = 'utf8mb4';         // Encodage UTF-8 complet

// Construction de la chaîne de connexion PDO
$dsn        = "mysql:host=$host;dbname=$db;charset=$charset";

// Options de configuration PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lever des exceptions en cas d'erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retourner les résultats sous forme de tableau associatif
];

try {
    // Établissement de la connexion à la base de données
    $pdo = new PDO($dsn, $user, $password, $options);
    
    // Démarrage d'une transaction pour garantir la cohérence des données
    $pdo->beginTransaction();
    
    // Liste des tables à récupérer dans l'ordre souhaité
    $tables = [
        'genres',        // Table des genres de films
        'realisateurs',  // Table des réalisateurs
        'acteurs',       // Table des acteurs
        'support',       // Table des supports (DVD, Blu-ray, etc.)
        'films',         // Table principale des films
        'film_acteur',   // Table de liaison entre films et acteurs (relation many-to-many)
    ];
    
    // Initialisation du tableau de résultats
    $result = [];
    
    // Récupération des données de chaque table
    foreach ($tables as $table) {
        // Exécution de la requête SELECT pour récupérer tous les enregistrements
        $stmt = $pdo->query("SELECT * FROM `$table`");
        // Stockage des résultats dans le tableau principal
        $result[$table] = $stmt->fetchAll();
    }
    
    // Récupération de la version la plus récente (pour le versioning de l'API)
    $stmt = $pdo->query("SELECT * FROM `version` ORDER BY `id` DESC LIMIT 1");
    $result['version'] = $stmt->fetch();
    
    // Validation de la transaction (toutes les opérations ont réussi)
    $pdo->commit();
    
    // Configuration de l'en-tête HTTP pour indiquer que la réponse est en JSON UTF-8
    header('Content-Type: application/json; charset=utf-8');
    
    // Envoi de la réponse JSON formatée avec les données récupérées
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    // Gestion des erreurs : annulation de la transaction si elle est en cours
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

   // Configuration du code de statut HTTP d'erreur
    http_response_code(500);
    
    // Configuration de l'en-tête pour la réponse d'erreur
    header('Content-Type: application/json; charset=utf-8');
    
    // Envoi d'une réponse JSON d'erreur avec message personnalisé
    echo json_encode([
        'error'   => 'Impossible de récupérer les données',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
