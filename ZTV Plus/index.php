<?php include './includes/header.php'; ?>
<?php
$pageTitle = "Accueil"; // Titre de la page
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/slider.css">
    <link href="https://fonts.googleapis.com/css2?family=FFF+Azadliq&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: 'FFF Azadliq', sans-serif;
            margin: 0;
            padding: 0;
        }

        .anime-slider {
            position: relative;
            width: 100%;
            height: 80vh;
            background: black;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            display: none;
        }

        .slide.active {
            opacity: 1;
            display: block;
        }

        .slide-content {
            position: absolute;
            bottom: 20%;
            left: 4%;
            width: 40%;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        .slide-background {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                to right,
                rgba(0, 0, 0, 0.8) 0%,
                rgba(0, 0, 0, 0.5) 40%,
                rgba(0, 0, 0, 0.1) 100%
            );
        }

        .slide h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .slide p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            line-height: 1.5;
        }

        .slide-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-play {
            background: white;
            color: black;
        }

        .btn-info {
            background: rgba(109, 109, 110, 0.7);
            color: white;
        }

        .slider-controls {
            position: absolute;
            bottom: 5%;
            right: 4%;
            display: flex;
            gap: 1rem;
            z-index: 3;
        }

        .prev, .next {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid white;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .titles {
            padding: 0 4%;
            margin-top: 20px;
        }

        .latest-releases, .current-series {
            display: flex;
            overflow-x: auto;
            padding: 10px 0;
            gap: 10px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .latest-releases::-webkit-scrollbar, 
        .current-series::-webkit-scrollbar {
            display: none;
        }

        .latest-releases img, .current-series img {
            width: 150px;
            height: auto;
            border-radius: 8px;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .latest-releases img:hover, .current-series img:hover {
            transform: scale(1.05);
        }

        h2 {
            font-family: 'FFF Azadliq', sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        @media (min-width: 768px) {
            .latest-releases img, .current-series img {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Slider pour afficher les tendances de la semaine -->
    <div class="anime-slider">
        <div class="slider-container">
            <?php
            $apiKey = 'd547a077baa00b34dcb5efb6440a4b04';
            $url = "https://api.themoviedb.org/3/trending/all/week?api_key={$apiKey}&language=fr-FR";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if (!empty($data['results'])) {
                foreach ($data['results'] as $index => $item) {
                    $title = htmlspecialchars($item['title'] ?? $item['name']);
                    $backdrop = 'https://image.tmdb.org/t/p/original' . $item['backdrop_path'];
                    $description = htmlspecialchars($item['overview']);
                    $formattedTitle = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    $link = $item['media_type'] === 'movie' ? "./movie/{$item['id']}-{$formattedTitle}" : "./tvshow/{$item['id']}-{$formattedTitle}";
                    echo "
                    <div class='slide " . ($index === 0 ? 'active' : '') . "'>
                        <img class='slide-background' src='{$backdrop}' alt='{$title}'>
                        <div class='slide-overlay'></div>
                        <div class='slide-content'>
                            <h2>{$title}</h2>
                            <p>{$description}</p>
                            <div class='slide-buttons'>
                                <a href='{$link}' class='btn btn-play'>
                                    <svg width='24' height='24' viewBox='0 0 24 24' fill='currentColor'>
                                        <path d='M8 5v14l11-7z'/>
                                    </svg>
                                    Lecture
                                </a>
                                <button class='btn btn-info'>
                                    <svg width='24' height='24' viewBox='0 0 24 24' fill='currentColor'>
                                        <path d='M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z'/>
                                    </svg>
                                    Plus d'infos
                                </button>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<p>Aucun contenu disponible pour le moment.</p>";
            }
            ?>
        </div>
        <div class="slider-controls">
            <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
            <button class="next" onclick="changeSlide(1)">&#10095;</button>
        </div>
    </div>

    <!-- Titres pour les sections -->
    <div class="titles">
        <h2>Dernières sorties</h2>
        <div class="latest-releases">
            <?php
            $urlLatest = "https://api.themoviedb.org/3/movie/now_playing?api_key={$apiKey}&language=fr-FR&page=1";
            $responseLatest = file_get_contents($urlLatest);
            $dataLatest = json_decode($responseLatest, true);

            if (!empty($dataLatest['results'])) {
                $latestCount = 0;
                foreach ($dataLatest['results'] as $latest) {
                    if ($latestCount >= 5) break;
                    $latestTitle = htmlspecialchars($latest['title']);
                    $latestPoster = 'https://image.tmdb.org/t/p/w500' . $latest['poster_path'];
                    $formattedTitle = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $latestTitle)));
                    echo "<a href='./movie/{$latest['id']}-{$formattedTitle}'><img src='{$latestPoster}' alt='{$latestTitle}' title='{$latestTitle}'></a>";
                    $latestCount++;
                }
            } else {
                echo "<p>Aucune dernière sortie disponible pour le moment.</p>";
            }
            ?>
        </div>

        <h2>Simulcast</h2>
        <div class="current-series">
            <?php
            $urlCurrentSeries = "https://api.themoviedb.org/3/tv/on_the_air?api_key={$apiKey}&language=fr-FR&page=1";
            $responseCurrentSeries = file_get_contents($urlCurrentSeries);
            $dataCurrentSeries = json_decode($responseCurrentSeries, true);

            if (!empty($dataCurrentSeries['results'])) {
                $seriesCount = 0;
                foreach ($dataCurrentSeries['results'] as $series) {
                    if ($seriesCount >= 5) break;
                    $seriesTitle = htmlspecialchars($series['name']);
                    $seriesPoster = 'https://image.tmdb.org/t/p/w500' . $series['poster_path'];
                    $formattedTitle = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $seriesTitle)));
                    echo "<a href='./tvshow/{$series['id']}-{$formattedTitle}'><img src='{$seriesPoster}' alt='{$seriesTitle}' title='{$seriesTitle}'></a>";
                    $seriesCount++;
                }
            } else {
                echo "<p>Aucune série actuellement diffusée disponible pour le moment.</p>";
            }
            ?>
        </div>
    </div>

    <?php include './includes/navbar.php'; ?>

    <script>
        function changeSlide(direction) {
            const slides = document.querySelectorAll('.slide');
            let currentIndex = 0;

            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });

            slides[currentIndex].classList.remove('active');
            slides[currentIndex].style.display = 'none';

            currentIndex += direction;
            if (currentIndex >= slides.length) currentIndex = 0;
            if (currentIndex < 0) currentIndex = slides.length - 1;

            slides[currentIndex].classList.add('active');
            slides[currentIndex].style.display = 'block';
        }

        // Changer automatiquement les slides toutes les 8 secondes
        setInterval(() => changeSlide(1), 8000);

        // Initialiser le premier slide
        document.addEventListener('DOMContentLoaded', function() {
            const firstSlide = document.querySelector('.slide');
            if (firstSlide) {
                firstSlide.classList.add('active');
                firstSlide.style.display = 'block';
            }
        });
    </script>
</body>
</html>