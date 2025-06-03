<?php
//Fichier de protection à inclure sur chaque page admin
session_start();

function verifierAuthentification() {
    if (!isset($_SESSION['utilisateur_id'])) {
        header('Location: login.php');
        exit;
    }
}

function deconnecter() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Vérification automatique
verifierAuthentification();
?>