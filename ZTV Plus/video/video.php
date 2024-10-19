                    <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);

                    class VideoDetails {
                        private $tmdb_id;
                        private $title_slug;
                        private $api_key;
                        private $data;

                        public function __construct() {
                            $this->api_key = 'd547a077baa00b34dcb5efb6440a4b04';
                            $this->initializeFromUrl();
                            $this->fetchVideoDetails();
                        }

                        private function initializeFromUrl() {
                            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            if (preg_match('/^\/video\/(\d+)-(.+)$/', $path, $matches)) {
                                $this->tmdb_id = $matches[1];
                                $this->title_slug = $matches[2];
                            } else {
                                $this->redirect404();
                            }
                        }

                        private function fetchVideoDetails() {
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
                            include '404.php';
                            exit;
                        }

                        public function render() {
                            $movie = $this->formatMovieData();
                            $videos = $this->getVideos();
                            ?>
                            <!DOCTYPE html>
                            <html lang="fr">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title><?= $movie['title'] ?> - Streaming</title>
                                <link href="https://fonts.googleapis.com/css2?family=Netflix+Sans:wght@300;400;700&display=swap" rel="stylesheet">
                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                                <style>
                                    :root {
                                        --primary-color: #e50914;
                                        --background-color: #141414;
                                        --text-color: #ffffff;
                                        --button-color: rgba(109, 109, 110, 0.7);
                                        --button-hover: rgba(109, 109, 110, 0.5);
                                        --button-active: #0077b6;
                                    }

                                    * {
                                        margin: 0;
                                        padding: 0;
                                        box-sizing: border-box;
                                    }

                                    body {
                                        font-family: 'Netflix Sans', Arial, sans-serif;
                                        background-color: var(--background-color);
                                        color: var(--text-color);
                                        line-height: 1.5;
                                    }

                                    .hero {
                                        position: relative;
                                        height: 56.25vw;
                                        max-height: 80vh;
                                        min-height: 350px;
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
                                        left: 4%;
                                        width: 92%;
                                        max-width: 600px;
                                    }

                                    .title {
                                        font-size: clamp(1.5rem, 5vw, 3rem);
                                        margin-bottom: 0.5rem;
                                        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
                                    }

                                    .metadata {
                                        font-size: clamp(0.8rem, 2vw, 1.1rem);
                                        margin-bottom: 1rem;
                                    }

                                    .metadata span {
                                        margin-right: 0.5em;
                                    }

                                    .synopsis {
                                        font-size: clamp(0.9rem, 2vw, 1.2rem);
                                        margin-bottom: 1.5rem;
                                        display: -webkit-box;
                                        -webkit-line-clamp: 3;
                                        -webkit-box-orient: vertical;
                                        overflow: hidden;
                                    }

                                    .buttons {
                                        display: flex;
                                        gap: 1rem;
                                        flex-wrap: wrap;
                                        justify-content: center;
                                        margin-bottom: 2rem;
                                    }

                                    .btn {
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
                                        background-color: var(--button-color);
                                        color: var(--text-color);
                                    }

                                    .btn-play {
                                        background-color: var(--text-color);
                                        color: var(--background-color);
                                    }

                                    .btn-play:hover {
                                        background-color: rgba(255, 255, 255, 0.75);
                                    }

                                    .btn-share:hover {
                                        background-color: var(--button-hover);
                                    }

                                    .btn-videos,
                                    .btn-next {
                                        margin-top: 1rem;
                                    }

                                    .btn-videos.active {
                                        background-color: var(--button-active);
                                        color: var(--text-color);
                                    }

                                    .match {
                                        color: #46d369;
                                        font-weight: bold;
                                    }

                                    .age-rating {
                                        border: 1px solid var(--text-color);
                                        padding: 0.1em 0.3em;
                                    }

                                    .video-section {
                                        margin-top: 2rem;
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        display: none;
                                    }

                                    .video-item {
                                        display: flex;
                                        align-items: center;
                                        background-color: #1f1f1f;
                                        border-radius: 5px;
                                        padding: 1rem;
                                        margin-bottom: 1rem;
                                        width: 80%;
                                    }

                                    .video-item img {
                                        width: 200px;
                                        height: auto;
                                        margin-right: 1.5rem;
                                    }

                                    .video-item-info {
                                        flex-grow: 1;
                                    }

                                    .video-item-title {
                                        font-size: 1.2rem;
                                        margin-bottom: 0.5rem;
                                    }

                                    .video-item-type {
                                        color: #999;
                                        font-size: 0.9rem;
                                    }

                                    @media (max-width: 768px) {
                                        .hero {
                                            height: 100vh;
                                        }

                                        .content {
                                            bottom: 10%;
                                        }

                                        .synopsis {
                                            -webkit-line-clamp: 2;
                                        }

                                        .video-item {
                                            flex-direction: column;
                                            align-items: flex-start;
                                            width: 100%;
                                        }

                                        .video-item img {
                                            width: 100%;
                                            margin-right: 0;
                                            margin-bottom: 1rem;
                                        }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="hero" style="background-image: url('https://image.tmdb.org/t/p/original<?= $movie['backdrop'] ?>');">
                                    <div class="content">
                                        <h1 class="title"><?= $movie['title'] ?></h1>
                                        <div class="metadata">
                                            <span class="match">Recommandé à <?= $movie['recommendation'] ?>%</span>
                                            <span><?= $movie['release_date'] ?></span>
                                            <span class="age-rating"><?= $movie['age_rating'] ?></span>
                                            <span><?= $movie['runtime'] ?></span>
                                            <span>HD</span>
                                        </div>
                                        <p class="synopsis"><?= $movie['overview'] ?></p>
                                        <div class="buttons">
                                            <button class="btn btn-play">
                                                <i class="fas fa-play"></i> Lecture
                                            </button>
                                            <button class="btn btn-share">
                                                <i class="fas fa-share"></i> Partager
                                            </button>
                                            <button class="btn btn-videos">
                                                <i class="fas fa-film"></i> Vidéos
                                            </button>
                                            <button class="btn btn-next">
                                                <i class="fas fa-list"></i> À voir ensuite
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="video-section">
                                    <h2>Vidéos</h2>
                                    <?php if (count($videos) > 0): ?>
                                        <?php foreach ($videos as $video): ?>
                                            <div class="video-item">
                                                <img src="https://img.youtube.com/vi/<?= $video['key'] ?>/0.jpg" alt="<?= $video['name'] ?>">
                                                <div class="video-item-info">
                                                    <h3 class="video-item-title"><?= $video['name'] ?></h3>
                                                    <p class="video-item-type"><?= $video['type'] ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>Aucune vidéo disponible.</p>
                                    <?php endif; ?>
                                </div>

                                <script>
                                    document.querySelectorAll('.btn-videos').forEach(btn => {
                                        btn.addEventListener('click', () => {
                                            const videoSection = document.querySelector('.video-section');
                                            const isActive = btn.classList.contains('active');

                                            if (isActive) {
                                                videoSection.style.display = 'none';
                                                btn.classList.remove('active');
                                            } else {
                                                videoSection.style.display = 'flex';
                                                btn.classList.add('active');
                                            }
                                        });
                                    });

                                    document.querySelector('.btn-share').addEventListener('click', function() {
                                        if (navigator.share) {
                                            navigator.share({
                                                title: '<?= addslashes($movie['title']) ?>',
                                                text: '<?= addslashes($movie['overview']) ?>',
                                                url: window.location.href
                                            }).then(() => {
                                                console.log('Partage réussi');
                                            }).catch((error) => {
                                                console.log('Erreur lors du partage', error);
                                            });
                                        } else {
                                            alert('Le partage n\'est pas supporté sur votre navigateur');
                                        }
                                    });
                                </script>
                            </body>
                            </html>
                            <?php
                        }

                        private function formatMovieData() {
                            $age_rating = $this->getAgeRating();
                            $recommendation = $this->calculateRecommendation();
                            return [
                                'title' => htmlspecialchars($this->data['title'] ?? 'Titre inconnu'),
                                'overview' => htmlspecialchars($this->data['overview'] ?? 'Aucune description disponible.'),
                                'backdrop' => $this->data['backdrop_path'] ?? '',
                                'release_date' => isset($this->data['release_date']) ? 
                                                date('Y', strtotime($this->data['release_date'])) : 
                                                'Date inconnue',
                                'runtime' => isset($this->data['runtime']) ? 
                                            $this->formatRuntime($this->data['runtime']) : 
                                            'Durée inconnue',
                                'age_rating' => $age_rating,
                                'recommendation' => $recommendation,
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
                            return 'TP'; // Tout Public par défaut
                        }

                        private function calculateRecommendation() {
                            if (isset($this->data['vote_average'])) {
                                // Convertir la note sur 10 en pourcentage
                                $percentage = round($this->data['vote_average'] * 10);
                                // Limiter le pourcentage entre 0 et 100
                                return max(0, min(100, $percentage));
                            }
                            return 'N/A'; // Si aucune note n'est disponible
                        }

                        private function getVideos() {
                            $videos = [];
                            if (isset($this->data['videos']['results'])) {
                                foreach ($this->data['videos']['results'] as $video) {
                                    if ($video['type'] === 'Trailer') {
                                        $videos[] = [
                                            'key' => $video['key'],
                                            'name' => $video['name'],
                                            'type' => 'Bande-annonce',
                                        ];
                                    } elseif ($video['type'] === 'Clip') {
                                        $videos[] = [
                                            'key' => $video['key'],
                                            'name' => $video['name'],
                                            'type' => 'Clip',
                                        ];
                                    }
                                }
                            }
                            return $videos;
                        }
                    }

                    $videoDetails = new VideoDetails();
                    $videoDetails->render();