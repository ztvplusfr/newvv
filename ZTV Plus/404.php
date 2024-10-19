<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - ZTV Plus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #ffffff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
        }

        .error-code {
            font-size: 8rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .error-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .error-message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #e6e6e6;
        }

        .back-button {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: #ffffff;
            color: #1e3c72;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .logo {
            margin-bottom: 2rem;
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 6rem;
            }

            .error-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">ZTV Plus</div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page non trouvée</h1>
        <p class="error-message">
            Désolé, la page que vous recherchez semble avoir disparu dans l'espace numérique. 
            Peut-être a-t-elle été déplacée ou supprimée.
        </p>
        <a href="/" class="back-button">Retourner à l'accueil</a>
    </div>

    <?php
    // Log l'erreur 404
    $error_message = "Erreur 404 - Page non trouvée: " . $_SERVER['REQUEST_URI'];
    error_log($error_message);

    // Vous pouvez ajouter ici d'autres logiques comme l'envoi d'une notification 
    // ou l'enregistrement dans une base de données
    ?>
</body>
</html>