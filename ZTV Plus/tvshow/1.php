<?php include './includes/header.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class TvShowDetails {
    private $tmdb_id;
    private $title_slug;
    private $api_key;
    private $data;

    public function __construct() {
        $this->api_key = 'd547a077baa00b34dcb5efb6440a4b04';
        $this->initializeFromUrl();
        $this->fetchTvShowDetails();
    }

    private function initializeFromUrl() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('/^\/tvshow\/(\d+)-(.+)$/', $path, $matches)) {
            $this->tmdb_id = $matches[1];
            $this->title_slug = $matches[2];
        } else {
            $this->redirect404();
        }
    }

    private function fetchTvShowDetails() {
        $url = sprintf(
            "https://api.themoviedb.org/3/tv/%s?api_key=%s&language=fr-FR&append_to_response=videos,credits,season_details",
            $this->tmdb_id,
            $this->api_key
        );

        $response = file_get_contents($url);

        if ($response === false) {
            $this->redirect404();
        }

        $this->data = json_decode($response, true);

        if (!$this->data || isset($this->data['success']) && $this->data['success'] === false) {
            $this->redirect404();
        }
    }

    private function redirect404() {
        header("HTTP/1.0 404 Not Found");
        include '../404.php';
        exit;
    }

    private function getEpisodesList() {
        if (!isset($this->data['seasons']) || !is_array($this->data['seasons'])) {
            return [];
        }

        $seasons = [];
        foreach ($this->data['seasons'] as $season) {
            $seasonNumber = $season['season_number'];
            if ($seasonNumber === 0) continue; // Skip specials/extras
            
            $url = sprintf(
                "https://api.themoviedb.org/3/tv/%s/season/%s?api_key=%s&language=fr-FR",
                $this->tmdb_id,
                $seasonNumber,
                $this->api_key
            );

            $response = file_get_contents($url);

            if ($response === false) {
                continue;
            }

            $seasonData = json_decode($response, true);
            if (isset($seasonData['episodes'])) {
                $seasons[$seasonNumber] = array_map(function($episode) {
                    return [
                        'episode_number' => $episode['episode_number'],
                        'name' => $episode['name'],
                        'overview' => $episode['overview'],
                        'still_path' => $episode['still_path'],
                        'air_date' => $episode['air_date'],
                        'runtime' => $episode['runtime'] ?? 0
                    ];
                }, $seasonData['episodes']);
            }
        }
        return $seasons;
    }

    public function render() {
        $tvShow = $this->formatTvShowData();
        $this->renderTvShowDetails($tvShow);
    }

    private function formatTvShowData() {
        return [
            'title' => $this->data['name'] ?? 'Titre inconnu',
            'poster_path' => $this->data['poster_path'] ?? '',
            'backdrop_path' => $this->data['backdrop_path'] ?? '',
            'vote_average' => $this->data['vote_average'] ?? 0,
            'first_air_date' => isset($this->data['first_air_date']) ? date('Y', strtotime($this->data['first_air_date'])) : 'Année inconnue',
            'number_of_seasons' => $this->data['number_of_seasons'] ?? 0,
            'age_rating' => $this->getAgeRating(),
            'language' => $this->getLanguage(),
            'genres' => $this->getGenres(),
            'overview' => $this->data['overview'] ?? 'Aucun synopsis disponible.',
        ];
    }

    private function renderTvShowDetails($tvShow) {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $tvShow['title'] ?> - Détails de la série</title>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <style>
                /* ... [Gardez tous les styles existants] ... */
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #141414;
                    color: #ffffff;
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }

                .hero {
                    position: relative;
                    height: 100vh;
                    background-size: cover;
                    background-position: center;
                }

                .hero::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(to bottom, rgba(20,20,20,0) 0%, rgba(20,20,20,0.8) 60%, rgba(20,20,20,1) 100%);
                }

                .content {
                    position: absolute;
                    bottom: 5%;
                    left: 5%;
                    right: 5%;
                    z-index: 1;
                }

                .movie-container {
                    display: flex;
                    max-width: 1200px;
                    margin: 0 auto;
                    align-items: flex-end;
                }

                .poster {
                    flex: 0 0 300px;
                    margin-right: 40px;
                }

                .poster img {
                    width: 100%;
                    height: auto;
                    border-radius: 8px;
                }

                .details {
                    flex: 1;
                }

                .title {
                    font-size: 2.5em;
                    margin-bottom: 10px;
                }

                .info {
                    font-size: 1.2em;
                    margin-bottom: 5px;
                }

                .recommendation {
                    color: #46d369;
                    font-weight: bold;
                }

                .age-rating, .language {
                    display: inline-block;
                    border: 1px solid #ffffff;
                    padding: 2px 5px;
                    border-radius: 3px;
                    margin-right: 10px;
                }

                .genres {
                    display: inline-block;
                    font-weight: bold;
                }

                .bold {
                    font-weight: bold;
                }

                .synopsis {
                    margin-top: 10px;
                    font-size: 1em;
                    line-height: 1.5;
                }

                .buttons {
                    display: flex;
                    gap: 1rem;
                    margin-top: 1rem;
                }

                .btn {
                    flex: 1;
                    padding: 0.8em 1.5em;
                    font-size: clamp(0.8rem, 2vw, 1.1rem);
                    border-radius: 4px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5em;
                    border: none;
                    transition: all 0.3s ease;
                    background-color: #333;
                    color: #fff;
                }

                .btn-play {
                    background-color: #e50914;
                }

                .btn-home {
                    background-color: #e50914;
                    color: #fff;
                    padding: 10px 20px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: bold;
                    position: absolute;
                    top: 20px;
                    left: 20px;
                    transition: background-color 0.3s ease;
                }

                .btn-home:hover {
                    background-color: #c40612;
                }

                @media (max-width: 767px) {
                    .movie-container {
                        flex-direction: column;
                    }

                    .poster {
                        display: none;
                    }

                    .content {
                        padding-top: 60vh;
                    }

                    .title {
                        font-size: 2em;
                    }

                    .info {
                        font-size: 1em;
                    }

                    .synopsis {
                        font-size: 0.9em;
                    }

                    .buttons {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        grid-gap: 1rem;
                    }

                    .btn {
                        width: 100%;
                    }

                    .btn-home {
                        padding: 8px 16px;
                        font-size: 12px;
                        top: 10px;
                        left: 10px;
                    }
                }
                /* Ajoutez les nouveaux styles pour la section épisodes */
                .episodes-section {
                    max-width: 1200px;
                    margin: 40px auto;
                    padding: 0 20px;
                }

                .season-selector {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .season-select {
                    background-color: #141414;
                    color: white;
                    border: 1px solid #404040;
                    padding: 8px 16px;
                    border-radius: 4px;
                    font-size: 1.1em;
                    cursor: pointer;
                }

                .view-options {
                    display: flex;
                    gap: 10px;
                }

                .view-button {
                    background-color: transparent;
                    border: 1px solid #404040;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .view-button.active {
                    background-color: #404040;
                }

                .episode-item {
                    display: flex;
                    margin-bottom: 20px;
                    background-color: #181818;
                    border-radius: 4px;
                    overflow: hidden;
                    transition: transform 0.3s ease;
                }

                .episode-item:hover {
                    transform: scale(1.02);
                    background-color: #282828;
                }

                .episode-thumbnail {
                    position: relative;
                    flex: 0 0 300px;
                }

                .episode-thumbnail img {
                    width: 100%;
                    height: 169px;
                    object-fit: cover;
                }

                .play-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }

                .episode-thumbnail:hover .play-overlay {
                    opacity: 1;
                }

                .play-overlay i {
                    font-size: 3em;
                    color: white;
                }

                .episode-details {
                    flex: 1;
                    padding: 20px;
                }

                .episode-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 10px;
                }

                .episode-header h3 {
                    margin: 0;
                    font-size: 1.2em;
                }

                .duration {
                    color: #999;
                }

                .episode-description {
                    color: #999;
                    margin: 0;
                    line-height: 1.5;
                }

                .episodes-section.grid-view .season-episodes {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 20px;
                }

                .episodes-section.grid-view .episode-item {
                    flex-direction: column;
                }

                .episodes-section.grid-view .episode-thumbnail {
                    flex: none;
                    width: 100%;
                }

                @media (max-width: 768px) {
                    .episode-item {
                        flex-direction: column;
                    }

                    .episode-thumbnail {
                        flex: none;
                        width: 100%;
                    }

                    .season-selector {
                        flex-direction: column;
                        gap: 10px;
                    }

                    .view-options {
                        width: 100%;
                        justify-content: center;
                    }

                    .season-select {
                        width: 100%;
                    }
                }
            </style>
        </head>
        <body>
            <div class="hero" style="background-image: url('https://image.tmdb.org/t/p/original<?= $tvShow['backdrop_path'] ?>');">
                <a href="/" class="btn-home">
                    <i class="fas fa-home"></i> Retour
                </a>
                <div class="content">
                    <div class="movie-container">
                        <div class="poster">
                            <img src="https://image.tmdb.org/t/p/w500<?= $tvShow['poster_path'] ?>" alt="<?= $tvShow['title'] ?> Poster">
                        </div>
                        <div class="details">
                            <h1 class="title"><?= $tvShow['title'] ?></h1>
                            <p class="info recommendation">Recommandé à <?= $tvShow['vote_average'] * 10 ?>%</p>
                            <p class="info">
                                <span class="bold"><?= $tvShow['first_air_date'] ?></span> |
                                <span class="bold"><?= $tvShow['number_of_seasons'] ?> saison<?= $tvShow['number_of_seasons'] > 1 ? 's' : '' ?></span>
                            </p>
                            <p class="info">
                                <span class="age-rating"><?= $tvShow['age_rating'] ?></span>
                                <span class="language"><?= $tvShow['language'] ?></span>
                                <span class="genres"><?= implode(', ', $tvShow['genres']) ?></span>
                            </p>
                            <p class="synopsis"><?= $tvShow['overview'] ?></p>
                            <div class="buttons">
                                <button class="btn btn-play">
                                    <i class="fas fa-play"></i> Lecture
                                </button>
                                <button class="btn btn-add">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="episodes-section">
                <div class="season-selector">
                    <select id="seasonSelect" class="season-select">
                        <?php 
                        $seasons = $this->getEpisodesList();
                        foreach ($seasons as $seasonNum => $episodes): ?>
                            <option value="<?= $seasonNum ?>">Saison <?= $seasonNum ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="view-options">
                        <button class="view-button active" data-view="list">
                            <i class="fas fa-list"></i> Liste
                        </button>
                        <button class="view-button" data-view="grid">
                            <i class="fas fa-th-large"></i> Grille
                        </button>
                    </div>
                </div>

                <?php foreach ($seasons as $seasonNum => $episodes): ?>
                    <div class="season-episodes" data-season="<?= $seasonNum ?>" <?= $seasonNum === 1 ? '' : 'style="display: none;"' ?>>
                        <?php foreach ($episodes as $episode): ?>
                            <div class="episode-item">
                                <div class="episode-thumbnail">
                                    <img src="https://image.tmdb.org/t/p/w300<?= $episode['still_path'] ?>" 
                                         alt="Episode <?= $episode['episode_number'] ?>" 
                                         onerror="this.src='path/to/placeholder.jpg'">
                                    <div class="play-overlay">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <div class="episode-details">
                                    <div class="episode-header">
                                        <h3><?= $episode['episode_number'] ?>. <?= htmlspecialchars($episode['name']) ?></h3>
                                        <span class="duration"><?= $episode['runtime'] ?> min</span>
                                    </div>
                                    <p class="episode-description"><?= htmlspecialchars($episode['overview']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const seasonSelect = document.getElementById('seasonSelect');
                const viewButtons = document.querySelectorAll('.view-button');
                const episodesSection = document.querySelector('.episodes-section');

                // Gestion du changement de saison
                seasonSelect.addEventListener('change', function() {
                    const selectedSeason = this.value;
                    document.querySelectorAll('.season-episodes').forEach(season => {
                        season.style.display = season.dataset.season === selectedSeason ? 'block' : 'none';
                    });
                });

                // Gestion du changement de vue (liste/grille)
                viewButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        viewButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        if (this.dataset.view === 'grid') {
                            episodesSection.classList.add('grid-view');
                        } else {
                            episodesSection.classList.remove('grid-view');
                        }
                    });
                });
            });
            </script>
        </body>
        </html>
        <?php
    }

    private function getAgeRating() {
        if (isset($this->data['content_ratings']['results'])) {
            foreach ($this->data['content_ratings']['results'] as $country) {
                if ($country['iso_3166_1'] === 'FR') {
                    return $country['rating'];
                }
            }
        }
        return 'TP';
    }

    private function getLanguage() {
        $original_language = $this->data['original_language'] ?? '';
        return ($original_language === 'fr') ? 'VF' : 'VOSTFR';
    }

    private function getGenres() {
        if (isset($this->data['genres']) && is_array($this->data['genres'])) {
            return array_map(function($genre) {
                return $genre['name'];
            }, $this->data['genres']);
        }
        return [];
    