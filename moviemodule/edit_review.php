<?php
session_start();

include '../components/comment_database.php';

$feedback_id = $_GET['feedback_id'] ?? 0;

if ($feedback_id == 0) {
    echo "Invalid feedback ID.";
    exit;
}

try {
    $pdo = getDatabaseConnection2();

    // Determine if the user is an admin
    $isAdmin = ($_SESSION['role'] === 'admin');

    // Fetch the review details, allow admins to bypass the user_id check
    if ($isAdmin) {
        $stmt = $pdo->prepare("SELECT * FROM comments_ratings WHERE feedback_id = ?");
        $stmt->execute([$feedback_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM comments_ratings WHERE feedback_id = ? AND user_id = ?");
        $stmt->execute([$feedback_id, $_SESSION['id']]);
    }

    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        echo "No review found or you don't have permission to edit this review.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_review = $_POST['comment'];
        $new_rating = $_POST['rating'];

        $update_stmt = $pdo->prepare("UPDATE comments_ratings SET comment = ?, rating = ? WHERE feedback_id = ?");
        $update_stmt->execute([$new_review, $new_rating, $feedback_id]);

        header("Location: movie_details.php?movie_id=" . $review['movie_id']);
        exit;
    }
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
    <title>Edit Review - Movie Review Paradise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-bg: #0a0a23;
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --accent-color: #ffd700;
            --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --border-radius: 15px;
            --shadow-soft: 0 5px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
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
            font-size: 3rem;
        }

        .shape:nth-child(2) {
            top: 70%;
            right: 15%;
            animation-delay: 2s;
            font-size: 2rem;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
            font-size: 2.5rem;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .edit-review-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1rem;
            position: relative;
            z-index: 2;
        }

        .edit-review-container {
            max-width: 600px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.6s ease-out;
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

        .edit-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q50,0 100,10 L100,20 L0,20 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }

        .edit-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .edit-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
            margin-bottom: 0;
        }

        .back-button {
            position: absolute;
            left: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            z-index: 3;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-50%) scale(1.05);
        }

        .edit-body {
            padding: 2.5rem;
        }

        .form-group {
            margin-bottom: 2rem;
            animation: slideInLeft 0.4s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .label-icon {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            outline: none;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .rating-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .star-rating {
            font-size: 2rem;
            cursor: pointer;
            color: #e9ecef;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .star-rating:hover {
            color: var(--accent-color);
            border-color: var(--accent-color);
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            background: rgba(255, 215, 0, 0.1);
        }

        .star-rating.active {
            color: var(--accent-color);
            border-color: var(--accent-color);
            background: rgba(255, 215, 0, 0.1);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .star-rating.preview {
            color: var(--accent-color);
            border-color: rgba(255, 215, 0, 0.7);
            transform: scale(1.05);
            background: rgba(255, 215, 0, 0.05);
        }

        .rating-input {
            display: none;
        }

        .rating-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .current-rating {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-top: 1rem;
        }

        .btn-custom {
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
            min-width: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Poppins', sans-serif;
        }

        .btn-update {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
            width: 100%;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            color: white;
        }

        .btn-cancel {
            background: var(--text-muted);
            color: white;
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
            text-decoration: none;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .form-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
            animation: slideInUp 0.4s ease-out 0.4s both;
        }

        @media (max-width: 768px) {
            .edit-review-page {
                padding: 1rem;
            }
            
            .edit-header {
                padding: 1.5rem 1rem;
            }
            
            .back-button {
                position: static;
                transform: none;
                margin-bottom: 1rem;
                display: inline-block;
            }
            
            .edit-title {
                font-size: 1.5rem;
                margin-top: 1rem;
            }
            
            .edit-body {
                padding: 2rem 1.5rem;
            }

            .rating-stars {
                gap: 0.3rem;
            }

            .star-rating {
                font-size: 1.8rem;
            }
        }

        /* Loading state */
        .btn-update.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-update.loading .btn-text {
            opacity: 0;
        }

        .btn-update.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="floating-shapes">
        <i class="fas fa-edit shape"></i>
        <i class="fas fa-star shape"></i>
        <i class="fas fa-comment shape"></i>
    </div>

    <div class="edit-review-page">
        <div class="edit-review-container">
            <div class="edit-header">
                <a href="movie_details.php?movie_id=<?= htmlspecialchars($review['movie_id']) ?>" class="back-button">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <h1 class="edit-title">
                    <i class="fas fa-edit me-2"></i>
                    Edit Review
                </h1>
                <p class="edit-subtitle">Update your thoughts and rating</p>
            </div>

            <div class="edit-body">
                <form method="POST" id="editReviewForm">
                    <div class="form-group">
                        <label for="comment" class="form-label">
                            <i class="fas fa-comment label-icon"></i>
                            Your Review
                        </label>
                        <textarea name="comment" id="comment" class="form-control" placeholder="Share your updated thoughts about this movie..." required><?= htmlspecialchars($review['comment']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="rating-section">
                            <label class="form-label">
                                <i class="fas fa-star label-icon"></i>
                                Your Rating
                            </label>
                            <p class="rating-text">Click on the stars to update your rating</p>
                            
                            <div class="rating-stars">
                                <i class="fas fa-star star-rating" data-rating="1" title="1 Star - Poor"></i>
                                <i class="fas fa-star star-rating" data-rating="2" title="2 Stars - Fair"></i>
                                <i class="fas fa-star star-rating" data-rating="3" title="3 Stars - Good"></i>
                                <i class="fas fa-star star-rating" data-rating="4" title="4 Stars - Very Good"></i>
                                <i class="fas fa-star star-rating" data-rating="5" title="5 Stars - Excellent"></i>
                            </div>
                            
                            <input type="hidden" name="rating" id="rating" value="<?= htmlspecialchars($review['rating']) ?>" required>
                            <div class="current-rating">
                                Current Rating: <span id="rating-display"><?= htmlspecialchars($review['rating']) ?></span>/5
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-custom btn-update" id="updateBtn">
                            <i class="fas fa-save me-2"></i>
                            <span class="btn-text">Update Review</span>
                        </button>
                        <a href="movie_details.php?movie_id=<?= htmlspecialchars($review['movie_id']) ?>" class="btn btn-custom btn-cancel">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-rating');
            const ratingInput = document.getElementById('rating');
            const ratingDisplay = document.getElementById('rating-display');
            const form = document.getElementById('editReviewForm');
            const updateBtn = document.getElementById('updateBtn');
            
            let currentRating = parseInt(ratingInput.value);

            // Initialize stars based on current rating
            updateStarDisplay(currentRating);

            // Star rating functionality
            stars.forEach((star, index) => {
                star.addEventListener('mouseenter', function() {
                    // Add preview class to hovered star and all previous stars
                    stars.forEach((s, i) => {
                        s.classList.remove('preview');
                        if (i <= index) {
                            s.classList.add('preview');
                        }
                    });
                });

                star.addEventListener('click', function() {
                    currentRating = index + 1;
                    ratingInput.value = currentRating;
                    ratingDisplay.textContent = currentRating;
                    updateStarDisplay(currentRating);
                    
                    // Remove preview classes after selection
                    stars.forEach(s => s.classList.remove('preview'));
                });
            });

            // Reset stars on mouse leave
            document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
                stars.forEach(s => s.classList.remove('preview'));
                updateStarDisplay(currentRating);
            });

            function updateStarDisplay(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }

            // Form submission with loading state
            form.addEventListener('submit', function() {
                updateBtn.classList.add('loading');
                updateBtn.disabled = true;
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                const comment = document.getElementById('comment').value.trim();
                const rating = ratingInput.value;

                if (!comment) {
                    e.preventDefault();
                    alert('Please provide a review comment.');
                    updateBtn.classList.remove('loading');
                    updateBtn.disabled = false;
                    return;
                }

                if (!rating || rating < 1 || rating > 5) {
                    e.preventDefault();
                    alert('Please select a rating between 1 and 5 stars.');
                    updateBtn.classList.remove('loading');
                    updateBtn.disabled = false;
                    return;
                }
            });

            // Character counter for textarea
            const textarea = document.getElementById('comment');
            const maxLength = 1000;
            
            // Create character counter
            const counter = document.createElement('div');
            counter.style.cssText = 'text-align: right; font-size: 0.8rem; color: #6c757d; margin-top: 0.25rem;';
            textarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${textarea.value.length}/${maxLength} characters`;
                
                if (remaining < 50) {
                    counter.style.color = '#dc3545';
                } else if (remaining < 100) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = '#6c757d';
                }
            }
            
            textarea.addEventListener('input', updateCounter);
            textarea.setAttribute('maxlength', maxLength);
            updateCounter();
        });
    </script>

    <?php include "../components/footer.php"; ?>
</body>
</html>