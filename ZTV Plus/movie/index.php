<?php include './includes/header.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class MovieDetails {
    private $tmdb_id;
    private $title_slug;
    private $api_key;
    private $data;

    public function __construct() {
        $this->api_key = 'd547a077baa00b34dcb5efb6440a4b04';
        $this->initializeFromUrl();
        $this->fetchMovieDetails();
    }

    private function initializeFromUrl() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('/^\/movie\/(\d+)-(.+)$/', $path, $matches)) {
            $this->tmdb_id = $matches[1];
            $this->title_slug = $matches[2];
        } else {
            $this->redirect404();
        }
    }

    private function fetchMovieDetails() {
        $url = sprintf(
            "https://api.themoviedb.org/3/movie/%s?api_key=%s&language=fr-FR&append_to_response=videos,credits,release_dates",
            $this->tmdb_id,
            $this->api_key
        );

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            $this->redirect404();
        }

        curl_close($ch);
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

    public function render() {
        $movie = $this->formatMovieData();
        ?>
                                        <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $movie['title'] ?> - Détails du film</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
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
        </style>
    </head>
    <body>
        <div class="hero" style="background-image: url('https://image.tmdb.org/t/p/original<?= $movie['backdrop_path'] ?>');">
            <a href="/" class="btn-home">
                <i class="fas fa-home"></i> Retour
            </a>
            <div class="content">
                <div class="movie-container">
                    <div class="poster">
                        <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" alt="<?= $movie['title'] ?> Poster">
                    </div>
                    <div class="details">
                        <h1 class="title"><?= $movie['title'] ?></h1>
                        <p class="info recommendation">Recommandé à <?= $movie['vote_average'] * 10 ?>%</p>
                        <p class="info">
                            <span class="bold"><?= $movie['release_year'] ?></span> |
                            <span class="bold"><?= $movie['runtime'] ?></span>
                        </p>
                        <p class="info">
                            <span class="age-rating"><?= $movie['age_rating'] ?></span>
                            <span class="language"><?= $movie['language'] ?></span>
                            <span class="genres"><?= implode(', ', $movie['genres']) ?></span>
                        </p>
                        <p class="synopsis"><?= $movie['overview'] ?></p>
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
    </body>
    </html>
    <?php
}

private function formatMovieData() {
    return [
        'title' => $this->data['title'] ?? 'Titre inconnu',
        'poster_path' => $this->data['poster_path'] ?? '',
        'backdrop_path' => $this->data['backdrop_path'] ?? '',
        'vote_average' => $this->data['vote_average'] ?? 0,
        'release_year' => isset($this->data['release_date']) ? date('Y', strtotime($this->data['release_date'])) : 'Année inconnue',
        'runtime' => isset($this->data['runtime']) ? $this->formatRuntime($this->data['runtime']) : 'Durée inconnue',
        'age_rating' => $this->getAgeRating(),
        'language' => $this->getLanguage(),
        'genres' => $this->getGenres(),
        'overview' => $this->data['overview'] ?? 'Aucun synopsis disponible.',
    ];
}

private function formatRuntime($runtime) {
    $hours = floor($runtime / 60);
    $minutes = $runtime % 60;
    return sprintf("%dh %02dmin", $hours, $minutes);
}

private function getAgeRating() {
    if (isset($this->data['release_dates']['results'])) {
        foreach ($this->data['release_dates']['results'] as $country) {
            if ($country['iso_3166_1'] === 'FR') {
                foreach ($country['release_dates'] as $release) {
                    if (isset($release['certification']) && $release['certification'] !== '') {
                        return $release['certification'];
                    }
                }
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
 }
}

$movieDetails = new MovieDetails();
$movieDetails->render();
?>