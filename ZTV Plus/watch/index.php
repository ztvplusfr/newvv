<?php
// watch.php

// Fonction pour récupérer l'URL à partir de l'API
function getVideoUrl($tmdb_id) {
    $apiUrl = "./getVideoUrl.php?tmdb_id=" . urlencode($tmdb_id);
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        return null; // Erreur lors de l'appel de l'API
    }

    $data = json_decode($response, true);
    return isset($data['url']) ? $data['url'] : null; // Assurez-vous que votre API retourne un champ 'url'
}

// Récupérer le tmdb_id depuis l'URL
if (isset($_GET['tmdb_id'])) {
    $tmdb_id = $_GET['tmdb_id'];
    $videoUrl = getVideoUrl($tmdb_id);

    if ($videoUrl === null) {
        echo "URL non trouvée pour ce programme.";
        exit;
    }
} else {
    echo "tmdb_id manquant.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regarder le programme</title>
</head>
<body>
    <h1>Regarder le programme</h1>
    <iframe src="<?php echo htmlspecialchars($videoUrl); ?>" width="100%" height="500" allowfullscreen></iframe>
    <br>
    <a href="./catalogue">Retour au catalogue</a>
</body>
</html>
