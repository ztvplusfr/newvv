<?php
// Inclure le fichier d'en-tête
include 'header.php';

// Lien de test fourni pour l'embed
$embed_url = "https://www.ztvplusfrance.x10.bz/embed.php?url=https://streamsilk.com/p/670e1d7b5d077&title=Dragon%20Ball:%20L%E2%80%99Aventure%20mystique%20-%20ZTV%20Plus";

// Extraire le titre de l'URL
preg_match('/title=([^&]+)/', $embed_url, $matches);
$title = isset($matches[1]) ? urldecode($matches[1]) : 'Lecture de Vidéo';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre fichier CSS -->
    <style>
        body {
            margin: 0;
            background-color: black; /* Fond noir */
            color: white; /* Texte blanc */
            font-family: 'FFF Azadliq', sans-serif; /* Police de caractère */
        }
        .header {
            position: absolute; /* Positionnement absolu pour le titre */
            top: 20px;
            left: 20px;
            z-index: 10;
        }
        .close-btn {
            background: red; /* Couleur de fond du bouton de fermeture */
            color: white; /* Couleur du texte du bouton de fermeture */
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            position: absolute; /* Positionnement absolu pour le bouton */
            top: 20px;
            right: 20px; /* Aligné à droite */
        }
        #video-container {
            position: fixed; /* Fixer le conteneur vidéo */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none; /* Pas de bordure pour l'iframe */
            -webkit-overflow-scrolling: touch; /* Pour un bon défilement sur iOS */
            display: block; /* Assurer un affichage en bloc */
        }
    </style>
</head>
<body>

<div class="header">
    <h1><?php echo $title; ?></h1> <!-- Afficher le titre -->
    <button class="close-btn" onclick="closePlayer()">X</button> <!-- Bouton de fermeture -->
</div>

<div id="video-container">
    <iframe 
        src="<?php echo $embed_url; ?>" 
        allowfullscreen 
        scrolling="no"></iframe> <!-- Intégrer l'iframe -->
</div>

<script>
    // Fonction pour fermer le lecteur et revenir à la page précédente
    function closePlayer() {
        window.history.back(); // Retourner à la page précédente
    }

    // Cacher le lecteur d'origine sur iOS
    if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
        document.addEventListener('webkitvisibilitychange', function() {
            if (document.webkitHidden) {
                document.querySelector('iframe').style.display = 'none'; // Cacher l'iframe
            } else {
                document.querySelector('iframe').style.display = 'block'; // Montrer l'iframe
            }
        });
    }
</script>

</body>
</html>
