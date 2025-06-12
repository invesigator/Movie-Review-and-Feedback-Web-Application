<?php
include "../components/header.php";

if (!isset($_SESSION['email'])) {
    header("location: ../usermodule/login.php");  // Ensure this path is correct based on your folder structure
    exit;
}

include "../components/movie_database.php";  // Include the database connection
$dbConnection = getDatabaseConnection1();  // Assuming this function returns the PDO connection

// Fetch all movies from the database and order them by movie_id in ascending order
$statement = $dbConnection->query("SELECT * FROM movies ORDER BY movie_id ASC");
$movies = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Movie Review Paradise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* HEADER COMPATIBILITY - MUST BE FIRST */
        .navbar {
            position: relative !important;
            z-index: 9999 !important;
            background-color: #f8f9fa !important;
            margin-bottom: 0 !important;
        }

        /* Ensure floating shapes don't cover header */
        .floating-shapes {
            z-index: 1 !important;
        }

        /* Adjust main content */
        main {
            position: relative !important;
            z-index: 2 !important;
            padding-top: 1rem !important; /* Reduced from 2rem to account for header */
        }

        /* Fix body styling */
        body {
            padding-top: 0 !important;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-bg: #0a0a23;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-light: #e6e6e6;
            --accent-color: #ffd700;
            --hover-transform: translateY(-10px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: 1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            color: white;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 70%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        main {
            position: relative;
            z-index: 2;
            padding-top: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 3rem 0;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: slideInDown 1s ease-out;
        }

        .page-subtitle {
            color: var(--text-light);
            font-size: 1.2rem;
            opacity: 0.9;
            animation: slideInDown 1s ease-out 0.3s both;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .movies-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .movie-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
            position: relative;
            animation: fadeInUp 0.6s ease-out;
        }

        .movie-card:hover {
            transform: var(--hover-transform);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .movie-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .movie-card:hover::before {
            opacity: 0.1;
        }

        .movie-poster-container {
            position: relative;
            overflow: hidden;
            height: 450px;
        }

        .movie-poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .movie-card:hover .movie-poster {
            transform: scale(1.05);
        }

        .poster-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.8) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: none; /* Hide the overlay completely */
        }

        .movie-card:hover .poster-overlay {
            opacity: 1;
        }

        .rating-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-color);
            color: #000;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .movie-details {
            padding: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .movie-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .movie-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .movie-title a:hover {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .movie-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .movie-info-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
        }

        .movie-info-item i {
            width: 20px;
            margin-right: 0.5rem;
            color: #667eea;
        }

        .movie-info-item strong {
            margin-right: 0.5rem;
            color: #333;
        }

        .review-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background: var(--primary-gradient);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 0.9rem;
        }

        .review-btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .review-btn i {
            margin-right: 0.5rem;
        }

        .genre-tag {
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .classification-badge {
            display: inline-block;
            background: var(--secondary-gradient);
            color: white;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .no-movies {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .no-movies i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .search-filter-bar {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            margin: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 1rem;
            font-weight: 500;
        }

        .search-input::placeholder {
            color: rgba(51, 51, 51, 0.7);
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
        }

        .filter-select {
            padding: 12px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 1rem;
            min-width: 150px;
            font-weight: 500;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
        }

        .filter-select option {
            background: white;
            color: #333;
            padding: 8px;
        }

        @media (max-width: 1200px) {
            .movies-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .movies-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .movies-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                padding: 1rem;
            }
            
            .search-filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input,
            .filter-select {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <i class="fas fa-film shape" style="font-size: 3rem;"></i>
        <i class="fas fa-star shape" style="font-size: 2rem;"></i>
        <i class="fas fa-ticket-alt shape" style="font-size: 2.5rem;"></i>
    </div>

    <main>
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-film me-3"></i>
                Movie Collection
            </h1>
            <p class="page-subtitle">Discover amazing movies and share your thoughts</p>
        </div>

        <div class="search-filter-bar">
            <input type="text" class="search-input" placeholder="Search movies..." id="movieSearch">
            <select class="filter-select" id="genreFilter">
                <option value="">All Genres</option>
                <option value="action">Action</option>
                <option value="drama">Drama</option>
                <option value="comedy">Comedy</option>
                <option value="thriller">Thriller</option>
                <option value="horror">Horror</option>
                <option value="romance">Romance</option>
                <option value="sci-fi">Sci-Fi</option>
            </select>
            <select class="filter-select" id="yearFilter">
                <option value="">All Years</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
            </select>
        </div>

        <div class="movies-container" id="moviesContainer">
            <?php if (empty($movies)) { ?>
                <div class="no-movies">
                    <i class="fas fa-film"></i>
                    <h3>No Movies Available</h3>
                    <p>Check back later for new additions to our collection.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($movies as $index => $movie) { ?>
                    <div class="movie-card" style="animation-delay: <?= $index * 0.1 ?>s;" data-genre="<?= strtolower(htmlspecialchars($movie['genre'])) ?>" data-year="<?= date('Y', strtotime($movie['release_date'])) ?>" data-title="<?= strtolower(htmlspecialchars($movie['title'])) ?>">
                        <div class="movie-poster-container">
                            <a href="movie_details.php?movie_id=<?= htmlspecialchars($movie['movie_id']) ?>">
                                <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?> Poster" class="movie-poster">
                            </a>
                            <div class="rating-badge">
                                <i class="fas fa-star me-1"></i>
                                8.5
                            </div>
                        </div>
                        
                        <div class="movie-details">
                            <h3 class="movie-title">
                                <a href="movie_details.php?movie_id=<?= htmlspecialchars($movie['movie_id']) ?>">
                                    <?= htmlspecialchars($movie['title']) ?>
                                </a>
                            </h3>
                            
                            <div class="movie-info">
                                <div class="movie-info-item">
                                    <i class="fas fa-tags"></i>
                                    <span class="genre-tag"><?= htmlspecialchars($movie['genre']) ?></span>
                                </div>
                                
                                <div class="movie-info-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= htmlspecialchars(date("d F Y", strtotime($movie['release_date']))) ?></span>
                                </div>
                                
                                <div class="movie-info-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span><?= htmlspecialchars($movie['director']) ?></span>
                                </div>
                                
                                <div class="movie-info-item">
                                    <i class="fas fa-certificate"></i>
                                    <span class="classification-badge"><?= htmlspecialchars($movie['classification']) ?></span>
                                </div>
                            </div>
                            
                            <a href="movie_details.php?movie_id=<?= htmlspecialchars($movie['movie_id']) ?>" class="review-btn">
                                <i class="fas fa-edit me-2"></i>
                                View & Review
                            </a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </main>
    
    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('movieSearch');
        const genreFilter = document.getElementById('genreFilter');
        const yearFilter = document.getElementById('yearFilter');
        const moviesContainer = document.getElementById('moviesContainer');
        const movieCards = document.querySelectorAll('.movie-card');

        function filterMovies() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedGenre = genreFilter.value.toLowerCase();
            const selectedYear = yearFilter.value;

            movieCards.forEach(card => {
                const title = card.dataset.title;
                const genre = card.dataset.genre;
                const year = card.dataset.year;

                const matchesSearch = title.includes(searchTerm);
                const matchesGenre = !selectedGenre || genre === selectedGenre;
                const matchesYear = !selectedYear || year === selectedYear;

                if (matchesSearch && matchesGenre && matchesYear) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.6s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterMovies);
        genreFilter.addEventListener('change', filterMovies);
        yearFilter.addEventListener('change', filterMovies);

        // Animate cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        movieCards.forEach(card => {
            observer.observe(card);
        });

        // Add loading effect for images
        document.querySelectorAll('.movie-poster').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
        });
    </script>
</body>
</html>

<?php
include "../components/footer.php";
?>