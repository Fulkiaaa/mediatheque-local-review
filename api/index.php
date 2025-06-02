<?php
$version_file = 'version_local.txt';
$data_file = 'donnees_cachees.json';

// Lire version locale
$version_locale = file_exists($version_file) ? trim(file_get_contents($version_file)) : '0.0.0';

// Appel API distante
$api_url = 'http://api-film/api.php';
$response = file_get_contents($api_url);

if ($response === false) {
    die("❌ Erreur : Impossible de contacter l’API distante.");
}

$data = json_decode($response, true);
if (!isset($data['films'][0]['numero_version'])) {
    die("❌ Erreur : version absente dans la réponse de l’API.");
}

// Version distante récupérée
$version_distante = $data['films'][0]['numero_version'];

// Affichage version distante
echo "<p>📦 Version disponible sur le serveur : <strong>$version_distante</strong></p>";
echo "<p>💾 Version locale actuelle : <strong>$version_locale</strong></p>";

// Comparaison
if (version_compare($version_distante, $version_locale, '>')) {
    echo "<p>🆕 Mise à jour nécessaire !</p>";

    // Mise à jour locale
    file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
    file_put_contents($version_file, $version_distante);

    echo "<p>✅ Données et version mises à jour localement.</p>";
} else {
    echo "<p>✅ Aucune mise à jour nécessaire.</p>";
}
?>
