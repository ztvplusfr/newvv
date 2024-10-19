<?php
// getVideoUrl.php

$servername = "sql110.infinityfree.com"; // ou votre serveur
$username = "if0_37117046"; // votre nom d'utilisateur DB
$password = "XnylBFN2i2hbgY"; // votre mot de passe DB
$dbname = "if0_37117046_api"; // votre base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer le tmdb_id
$tmdb_id = isset($_GET['tmdb_id']) ? $_GET['tmdb_id'] : '';

// Requête pour obtenir l'URL
$sql = "SELECT url FROM programmes WHERE tmdb_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tmdb_id);
$stmt->execute();
$result = $stmt->get_result();

// Récupérer les résultats
$response = array();
if ($row = $result->fetch_assoc()) {
    $response['url'] = $row['url'];
} else {
    $response['url'] = null; // Aucune URL trouvée
}

// Retourner la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);

// Fermer la connexion
$conn->close();
?>
