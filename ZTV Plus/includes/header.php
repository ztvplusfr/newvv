<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=FFF+Azadliq&display=swap" rel="stylesheet"> <!-- Lien pour la police -->
    <link rel="stylesheet" href="/css/navbar.css">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: 'FFF Azadliq', sans-serif; /* Utiliser la police FFF Azadliq */
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #141414;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 32px;
            margin-right: 20px;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-link {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #e6e6e6;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .search-btn,
        .notification-btn,
        .profile-btn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover,
        .notification-btn:hover,
        .profile-btn:hover {
            background-color: #555;
        }

        .notification-btn svg,
        .search-btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .profile-btn {
            display: flex;
            align-items: center;
        }

        .profile-btn img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
        }

        @media (max-width: 767px) {
            .header {
                padding: 10px;
            }

            .logo img {
                height: 24px;
                margin-right: 10px;
            }

            .nav-link {
                font-size: 12px;
                margin-left: 10px;
            }

            .search-btn,
            .notification-btn,
            .profile-btn {
                padding: 6px 10px;
                font-size: 12px;
                margin-left: 8px;
            }

            .notification-btn svg,
            .search-btn svg {
                width: 16px;
                height: 16px;
            }

            .profile-btn img {
                width: 24px;
                height: 24px;
                margin-right: 6px;
            }

            /* Afficher la navbar.php uniquement pour les petits écrans */
            .navbar {
                display: block;
            }

            @media (min-width: 768px) {
                .navbar {
                    display: none;
                }
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Netflix_2015_logo.svg/1280px-Netflix_2015_logo.svg.png" alt="Netflix Logo">
            </div>
            <div class="nav-links">
                <a href="#" class="nav-link">Accueil</a>
                <a href="#" class="nav-link">Séries</a>
                <a href="#" class="nav-link">Films</a>
                <a href="#" class="nav-link">Nouveautés</a>
                <a href="#" class="nav-link">Ma Liste</a>
            </div>
        </div>
        <div class="header-right">
            <button class="search-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                </svg>
            </button>
            <button class="notification-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 25.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416l400 0c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4l0-25.4c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm0 96c61.9 0 112 50.1 112 112l0 25.4c0 47.9 13.9 94.6 39.7 134.6L72.3 368C98.1 328 112 281.3 112 233.4l0-25.4c0-61.9 50.1-112 112-112zm64 352l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7s18.7-28.3 18.7-45.3z"/>
                </svg>
            </button>
            <button class="profile-btn">
                <img src="https://via.placeholder.com/32" alt="Profile Picture">
                <span>Nom d'utilisateur</span>
            </button>
        </div>
    </header>

    <!-- Afficher la navbar.php uniquement pour les petits écrans -->
    <?php include './includes/navbar.php'; ?>