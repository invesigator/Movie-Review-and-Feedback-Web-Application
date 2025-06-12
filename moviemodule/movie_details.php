<?php
session_start();

include '../components/header.php';
include '../components/movie_database.php';

$movie_id = $_GET['movie_id'] ?? 0;
if ($movie_id == 0) {
    echo "No movie found";
    exit;
}

try {
    $pdo = getDatabaseConnection1();
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        echo "No movie found with ID " . htmlspecialchars($movie_id);
        exit;
    }

    $comments_stmt = $pdo->prepare(
        "SELECT c.*, u.first_name, u.last_name
         FROM comments_ratings AS c 
         JOIN users AS u ON c.user_id = u.id 
         WHERE c.movie_id = ?"
    );
    $comments_stmt->execute([$movie_id]);
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['title']) ?> - Movie Review Paradise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: 1;
            pointer-events: none;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            top: 0;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            color: white;
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
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
            padding: 2rem 0;
            margin-top: 20px;
        }

        .movie-details-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .movie-info {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 3rem;
            align-items: start;
        }

        .movie-poster {
            position: relative;
        }

        .movie-poster img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .movie-poster:hover img {
            transform: scale(1.05);
        }

        .movie-details {
            padding-left: 1rem;
        }

        .movie-title {
            font-size: 3rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .movie-meta {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            color: #555;
        }

        .meta-icon {
            width: 30px;
            margin-right: 1rem;
            color: #667eea;
            font-size: 1.2rem;
        }

        .meta-label {
            font-weight: 600;
            margin-right: 0.5rem;
            color: #333;
            min-width: 120px;
        }

        .genre-tag {
            display: inline-block;
            background: var(--primary-gradient);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .classification-badge {
            display: inline-block;
            background: var(--secondary-gradient);
            color: white;
            padding: 8px 15px;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }

        .review-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.8s ease-out 0.3s both;
        }

        .card {
            border: none;
            background: transparent;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            padding: 1.5rem 2rem;
            border: none;
            text-align: center;
        }

        .card-body {
            padding: 2.5rem;
        }

        .rating-summary {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            margin-bottom: 2rem;
        }

        .average-rating {
            font-size: 4rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
        }

        .rating-stars {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .total-reviews {
            font-size: 1.3rem;
            color: #666;
            font-weight: 500;
        }

        .star-light {
            color: #e9ecef;
        }

        .text-warning {
            color: var(--accent-color) !important;
        }

        .progress-section {
            padding: 1rem;
        }

        .progress-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .progress-label-left {
            display: flex;
            align-items: center;
            min-width: 80px;
            font-weight: 600;
            color: #333;
        }

        .progress {
            flex: 1;
            height: 12px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: var(--accent-color);
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-label-right {
            min-width: 50px;
            text-align: right;
            color: #666;
            font-weight: 500;
        }

        .write-review-section {
            text-align: center;
            padding: 2rem;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 20px;
        }

        .write-review-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .btn-review {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-review:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .reviews-section {
            margin-top: 3rem;
        }

        .review-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-5px);
        }

        .review-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .reviewer-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .review-rating {
            font-size: 1.1rem;
            margin: 1rem 0;
        }

        .review-text {
            color: #555;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .review-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .review-date {
            color: #999;
            font-size: 0.9rem;
        }

        .review-actions {
            display: flex;
            gap: 1rem;
        }

        .review-actions a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .review-actions a:hover {
            color: #764ba2;
        }

        .review-actions a.delete-review:hover {
            color: #dc3545;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            z-index: 1070;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 2rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 2rem;
        }

        .submit_star {
            font-size: 2rem;
            margin: 0 0.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit_star:hover {
            transform: scale(1.2);
        }

        #user_review {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1rem;
            font-size: 1rem;
            min-height: 120px;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        #user_review:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .btn-submit {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .no-reviews {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .no-reviews i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .movie-info {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }

            .movie-title {
                font-size: 2rem;
            }

            .movie-details-container,
            .review-container {
                margin: 1rem;
                padding: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .rating-summary {
                padding: 1.5rem;
            }

            .average-rating {
                font-size: 3rem;
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
        <div class="movie-details-container">
            <div class="movie-info">
                <div class="movie-poster">
                    <img src="../images/<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?> Poster">
                </div>
                <div class="movie-details">
                    <h1 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h1>
                    <div class="movie-meta">
                        <div class="meta-item">
                            <i class="fas fa-tags meta-icon"></i>
                            <span class="meta-label">Genre:</span>
                            <span class="genre-tag"><?= htmlspecialchars($movie['genre']) ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt meta-icon"></i>
                            <span class="meta-label">Release Date:</span>
                            <span><?= htmlspecialchars(date("d F Y", strtotime($movie['release_date']))) ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user-tie meta-icon"></i>
                            <span class="meta-label">Director:</span>
                            <span><?= htmlspecialchars($movie['director']) ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-certificate meta-icon"></i>
                            <span class="meta-label">Classification:</span>
                            <span class="classification-badge"><?= htmlspecialchars($movie['classification']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="review-container">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-star me-2"></i>
                    <?= htmlspecialchars($movie['title']) ?> Reviews
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="rating-summary">
                                <div class="average-rating" id="average_rating">0.0</div>
                                <div class="rating-stars">
                                    <i class="fas fa-star star-light main_star"></i>
                                    <i class="fas fa-star star-light main_star"></i>
                                    <i class="fas fa-star star-light main_star"></i>
                                    <i class="fas fa-star star-light main_star"></i>
                                    <i class="fas fa-star star-light main_star"></i>
                                </div>
                                <div class="total-reviews">
                                    <span id="total_review">0</span> Reviews
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="progress-section">
                                <div class="progress-item">
                                    <div class="progress-label-left">
                                        <b>5</b> <i class="fas fa-star text-warning ms-1"></i>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="five_star_progress"></div>
                                    </div>
                                    <div class="progress-label-right">(<span id="total_five_star_review">0</span>)</div>
                                </div>
                                <div class="progress-item">
                                    <div class="progress-label-left">
                                        <b>4</b> <i class="fas fa-star text-warning ms-1"></i>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="four_star_progress"></div>
                                    </div>
                                    <div class="progress-label-right">(<span id="total_four_star_review">0</span>)</div>
                                </div>
                                <div class="progress-item">
                                    <div class="progress-label-left">
                                        <b>3</b> <i class="fas fa-star text-warning ms-1"></i>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="three_star_progress"></div>
                                    </div>
                                    <div class="progress-label-right">(<span id="total_three_star_review">0</span>)</div>
                                </div>
                                <div class="progress-item">
                                    <div class="progress-label-left">
                                        <b>2</b> <i class="fas fa-star text-warning ms-1"></i>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="two_star_progress"></div>
                                    </div>
                                    <div class="progress-label-right">(<span id="total_two_star_review">0</span>)</div>
                                </div>
                                <div class="progress-item">
                                    <div class="progress-label-left">
                                        <b>1</b> <i class="fas fa-star text-warning ms-1"></i>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" id="one_star_progress"></div>
                                    </div>
                                    <div class="progress-label-right">(<span id="total_one_star_review">0</span>)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="write-review-section">
                                <h3 class="write-review-title">Share Your Thoughts</h3>
                                <p class="mb-3">Help others discover great movies!</p>
                                <button type="button" name="add_review" id="add_review" class="btn btn-review">
                                    <i class="fas fa-edit me-2"></i>
                                    Write Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="reviews-section" id="review_content">
            <!-- Reviews will be loaded here -->
        </div>
    </main>

    <!-- Modal for Review -->
    <div id="review_modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star me-2"></i>
                        Submit Your Review
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h6 class="mb-3">Rate this movie:</h6>
                        <div class="rating-stars">
                            <i class="fas fa-star star-light submit_star" id="submit_star_1" data-rating="1"></i>
                            <i class="fas fa-star star-light submit_star" id="submit_star_2" data-rating="2"></i>
                            <i class="fas fa-star star-light submit_star" id="submit_star_3" data-rating="3"></i>
                            <i class="fas fa-star star-light submit_star" id="submit_star_4" data-rating="4"></i>
                            <i class="fas fa-star star-light submit_star" id="submit_star_5" data-rating="5"></i>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="user_review" class="form-label">Your Review:</label>
                        <textarea name="user_review" id="user_review" class="form-control" placeholder="Share your thoughts about this movie..." rows="5"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-submit" id="save_review">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // --- START OF FIX ---
        // Safely pass PHP variables to JavaScript. This is the most important part.
        const currentUserId = <?= isset($_SESSION['id']) ? json_encode($_SESSION['id']) : 'null' ?>;
        const isCurrentUserAdmin = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false' ?>;
        const movieId = <?= json_encode($movie_id) ?>;
        const isAuthenticated = <?= isset($_SESSION['id']) ? 'true' : 'false' ?>;
        // --- END OF FIX ---

        document.addEventListener('DOMContentLoaded', function () {
            var rating_data = 0;
            var reviewModal; // Declare modal instance variable

            // Initialize the modal once the DOM is ready
            var modalElement = document.getElementById('review_modal');
            if (modalElement) {
                reviewModal = new bootstrap.Modal(modalElement);
            }

            document.getElementById('add_review').addEventListener('click', function () {
                // --- FIX: Check if user is logged in before showing modal ---
                if (!isAuthenticated) {
                    alert("You must be logged in to write a review.");
                    // Optional: Redirect to login page
                    // window.location.href = '../usermodule/login.php'; 
                    return;
                }
                if(reviewModal) {
                    reviewModal.show();
                }
            });

            document.querySelectorAll('.submit_star').forEach(function (element) {
                element.addEventListener('mouseenter', function () {
                    var rating = this.getAttribute('data-rating');
                    reset_background();
                    for (var count = 1; count <= rating; count++) {
                        document.getElementById('submit_star_' + count).classList.add('text-warning');
                    }
                });
            });

            document.querySelectorAll('.submit_star').forEach(function (element) {
                element.addEventListener('mouseleave', function () {
                    reset_background();
                    for (var count = 1; count <= rating_data; count++) {
                        document.getElementById('submit_star_' + count).classList.remove('star-light');
                        document.getElementById('submit_star_' + count).classList.add('text-warning');
                    }
                });
            });

            document.querySelectorAll('.submit_star').forEach(function (element) {
                element.addEventListener('click', function () {
                    rating_data = this.getAttribute('data-rating');
                });
            });

            document.getElementById('save_review').addEventListener('click', function () {
                var user_review = document.getElementById('user_review').value;

                if (user_review.trim() === '' || rating_data == 0) {
                    alert("Please provide both a rating and a review!");
                    return false;
                } else {
                    var xhr = new XMLHttpRequest();
                    // --- FIX: Use the JS constant for movie_id ---
                    xhr.open("POST", "submit_rating.php?movie_id=" + movieId, true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                            if(reviewModal) {
                                reviewModal.hide();
                            }
                            document.getElementById('user_review').value = '';
                            rating_data = 0;
                            reset_background();
                            load_rating_data();
                            alert('Review submitted successfully!');
                        }
                    };
                    xhr.send("rating_data=" + rating_data + "&user_review=" + encodeURIComponent(user_review));
                }
            });

            // FIXED: Single confirmation dialog for delete
            document.getElementById('review_content').addEventListener('click', function(event) {
                if (event.target.closest('.delete-review')) {
                    if (!confirm("Are you sure you want to delete this review?")) {
                        event.preventDefault();
                    }
                }
            });

            function reset_background() {
                document.querySelectorAll('.submit_star').forEach(function (element) {
                    element.classList.add('star-light');
                    element.classList.remove('text-warning');
                });
            }

            function load_rating_data() {
                var xhr = new XMLHttpRequest();
                // --- FIX: Use the JS constant for movie_id ---
                xhr.open("POST", "submit_rating.php?movie_id=" + movieId, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.responseType = 'json';
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        var data = xhr.response;
                        
                        // Add a check for valid data to prevent further errors
                        if (!data) {
                            console.error("Received invalid data from submit_rating.php");
                            return;
                        }

                        document.getElementById('average_rating').textContent = data.average_rating;
                        document.getElementById('total_review').textContent = data.total_review;

                        var count_star = 0;
                        document.querySelectorAll('.main_star').forEach(function (element) {
                            count_star++;
                            if (Math.ceil(data.average_rating) >= count_star) {
                                element.classList.add('text-warning');
                                element.classList.remove('star-light');
                            } else {
                                element.classList.remove('text-warning');
                                element.classList.add('star-light');
                            }
                        });

                        document.getElementById('total_five_star_review').textContent = data.five_star_review;
                        document.getElementById('total_four_star_review').textContent = data.four_star_review;
                        document.getElementById('total_three_star_review').textContent = data.three_star_review;
                        document.getElementById('total_two_star_review').textContent = data.two_star_review;
                        document.getElementById('total_one_star_review').textContent = data.one_star_review;

                        if (data.total_review > 0) {
                            document.getElementById('five_star_progress').style.width = (data.five_star_review / data.total_review) * 100 + '%';
                            document.getElementById('four_star_progress').style.width = (data.four_star_review / data.total_review) * 100 + '%';
                            document.getElementById('three_star_progress').style.width = (data.three_star_review / data.total_review) * 100 + '%';
                            document.getElementById('two_star_progress').style.width = (data.two_star_review / data.total_review) * 100 + '%';
                            document.getElementById('one_star_progress').style.width = (data.one_star_review / data.total_review) * 100 + '%';
                        } else {
                            // Reset progress bars if there are no reviews
                            document.getElementById('five_star_progress').style.width = '0%';
                            document.getElementById('four_star_progress').style.width = '0%';
                            document.getElementById('three_star_progress').style.width = '0%';
                            document.getElementById('two_star_progress').style.width = '0%';
                            document.getElementById('one_star_progress').style.width = '0%';
                        }

                        if (data.review_data && data.review_data.length > 0) {
                            var html = '<div class="container"><div class="row">';
                            data.review_data.forEach(function (review) {
                                html += '<div class="col-12">';
                                html += '<div class="review-card">';
                                html += '<div class="review-header">';
                                html += '<div class="reviewer-name">' + review.user_name + '</div>';
                                html += '</div>';
                                html += '<div class="review-rating">';
                                for (var star = 1; star <= 5; star++) {
                                    var class_name = review.rating >= star ? 'text-warning' : 'star-light';
                                    html += '<i class="fas fa-star ' + class_name + ' me-1"></i>';
                                }
                                html += '</div>';
                                html += '<div class="review-text">' + review.user_review + '</div>';
                                html += '<div class="review-footer">';
                                html += '<div class="review-date">Posted on ' + review.datetime + '</div>';
                                html += '<div class="review-actions">';
                                
                                // --- FIXED: Single confirmation only ---
                                // Use the safe JS constants for the check
                                if (isAuthenticated && (review.user_id == currentUserId || isCurrentUserAdmin)) {
                                    html += '<a href="edit_review.php?feedback_id=' + review.feedback_id + '" class="edit-review me-3">';
                                    html += '<i class="fas fa-edit me-1"></i>Edit</a>';
                                    html += '<a href="delete_review.php?feedback_id=' + review.feedback_id + '&movie_id=' + movieId + '" class="delete-review text-danger">';
                                    html += '<i class="fas fa-trash me-1"></i>Delete</a>';
                                }
                                
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            });
                            html += '</div></div>';
                            document.getElementById('review_content').innerHTML = html;
                        } else {
                            document.getElementById('review_content').innerHTML = 
                                '<div class="container">' +
                                '<div class="no-reviews">' +
                                '<i class="fas fa-comment-slash"></i>' +
                                '<h4>No Reviews Yet</h4>' +
                                '<p>Be the first to share your thoughts about this movie!</p>' +
                                '</div>' +
                                '</div>';
                        }
                    }
                };
                xhr.send("action=load_data");
            }

            load_rating_data();
        });
    </script>
</body>
</html>

<?php
include "../components/footer.php";
?>