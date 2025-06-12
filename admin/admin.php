<?php
session_start();
include "../components/movie_database.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("location: ../index.php");
    exit;
}

$dbConnection = getDatabaseConnection1();

$success_message = $error_message = "";

// Initialize variables
$title = $genre = $release_date = $director = $classification = $poster_url = "";
$edit_mode = false;
$movie_id = null;

// Handle form submission (Create or Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $director = $_POST['director'];
    $classification = $_POST['classification'];
    
    // Handle image upload
    $poster_url = $_POST['poster_url_text']; // fallback to text input
    
    if (isset($_FILES['poster_upload']) && $_FILES['poster_upload']['error'] == 0) {
        $allowed_ext = array("jpg", "jpeg", "png", "gif", "webp");
        $file_name = $_FILES['poster_upload']['name'];
        $file_size = $_FILES['poster_upload']['size'];
        $file_tmp = $_FILES['poster_upload']['tmp_name'];
        
        $file_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_parts));

        if (!in_array($file_ext, $allowed_ext)) {
            $error_message = "Invalid file type. Please use JPG, JPEG, PNG, GIF, or WEBP.";
        } elseif ($file_size > 5097152) { // 5 MB
            $error_message = "File size must be less than 5 MB.";
        } else {
            $target_dir = "../images/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Create unique filename to avoid conflicts
            $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $unique_name;
            
            if (move_uploaded_file($file_tmp, $target_file)) {
                $poster_url = "../images/" . $unique_name;
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Only proceed if no upload errors
    if (empty($error_message)) {
        if (isset($_POST['movie_id']) && !empty($_POST['movie_id'])) {
            // Update movie
            $movie_id = $_POST['movie_id'];
            $statement = $dbConnection->prepare(
                "UPDATE movies SET title = ?, genre = ?, release_date = ?, director = ?, classification = ?, poster_url = ? WHERE movie_id = ?"
            );
            if ($statement->execute([$title, $genre, $release_date, $director, $classification, $poster_url, $movie_id])) {
                $success_message = "Movie updated successfully!";
            } else {
                $error_message = "Failed to update movie.";
            }
        } else {
            // Create new movie
            $statement = $dbConnection->prepare(
                "INSERT INTO movies (title, genre, release_date, director, classification, poster_url) VALUES (?, ?, ?, ?, ?, ?)"
            );
            if ($statement->execute([$title, $genre, $release_date, $director, $classification, $poster_url])) {
                $success_message = "Movie added successfully!";
            } else {
                $error_message = "Failed to add movie.";
            }
        }
        
        // Redirect to prevent form resubmission
        if (empty($error_message)) {
            header("location: admin.php");
            exit;
        }
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $movie_id = $_GET['delete'];
    $statement = $dbConnection->prepare("DELETE FROM movies WHERE movie_id = ?");
    if ($statement->execute([$movie_id])) {
        $success_message = "Movie deleted successfully!";
    } else {
        $error_message = "Failed to delete movie.";
    }
    
    header("location: admin.php");
    exit;
}

// Handle edit request
if (isset($_GET['edit'])) {
    $movie_id = $_GET['edit'];
    $statement = $dbConnection->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $statement->execute([$movie_id]);
    $movie = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($movie) {
        $title = $movie['title'];
        $genre = $movie['genre'];
        $release_date = $movie['release_date'];
        $director = $movie['director'];
        $classification = $movie['classification'];
        $poster_url = $movie['poster_url'];
    
        $edit_mode = true;
    }
}

// Fetch all movies for display
$movies = $dbConnection->query("SELECT * FROM movies ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Movie Review Paradise</title>
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
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
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
            opacity: 0.08;
            color: white;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }

        .shape:nth-child(1) {
            top: 10%;
            left: 5%;
            animation-delay: 0s;
            font-size: 2.5rem;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 3s;
            font-size: 2rem;
        }

        .shape:nth-child(3) {
            bottom: 15%;
            left: 15%;
            animation-delay: 6s;
            font-size: 3rem;
        }

        .shape:nth-child(4) {
            top: 30%;
            right: 30%;
            animation-delay: 1.5s;
            font-size: 1.8rem;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-15px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }

        .admin-container {
            position: relative;
            z-index: 2;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .admin-header {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            animation: slideInDown 0.6s ease-out;
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

        .admin-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .admin-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .alert-container {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1000;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .admin-alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            box-shadow: var(--shadow-medium);
            min-width: 300px;
        }

        .admin-alert-success {
            background: var(--success-gradient);
            color: white;
        }

        .admin-alert-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .admin-content {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 2rem;
            align-items: start;
        }

        .form-section {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            animation: slideInUp 0.4s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.15s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .form-group:nth-child(4) { animation-delay: 0.25s; }
        .form-group:nth-child(5) { animation-delay: 0.3s; }
        .form-group:nth-child(6) { animation-delay: 0.35s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-text {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .btn-custom {
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: var(--text-muted);
            color: white;
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .table-section {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInRight 0.6s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .table-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .movie-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: 15px;
            min-width: 120px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .admin-table {
            margin-bottom: 0;
            background: white;
        }

        .admin-table thead th {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            padding: 1rem;
            border: none;
        }

        .admin-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .admin-table tbody tr {
            transition: all 0.3s ease;
        }

        .admin-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-1px);
        }

        .movie-poster {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .movie-poster:hover {
            transform: scale(1.1);
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            margin: 0 2px;
        }

        .btn-edit {
            background: var(--warning-gradient);
            color: white;
        }

        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.4);
            color: white;
        }

        .btn-delete {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4);
            color: white;
        }

        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-medium);
        }

        .modal-header {
            background: var(--danger-gradient);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 1.5rem 2rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
            font-size: 1.1rem;
            color: var(--text-dark);
        }

        .poster-upload-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 2px dashed #667eea;
            text-align: center;
            transition: all 0.3s ease;
        }

        .poster-upload-section:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border-color: #764ba2;
        }

        .poster-upload-section.dragover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            border-color: #764ba2;
            transform: scale(1.02);
        }

        #posterPreview {
            text-align: center;
        }

        .preview-image {
            max-width: 200px;
            max-height: 300px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            transition: transform 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
        }

        .preview-container {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            display: inline-block;
            box-shadow: var(--shadow-soft);
        }

        .file-info {
            background: rgba(102, 126, 234, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-dark);
            margin-top: 0.5rem;
            display: inline-block;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h4 {
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        @media (max-width: 1200px) {
            .admin-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }

            .admin-header {
                padding: 1.5rem;
            }

            .admin-title {
                font-size: 2rem;
            }

            .form-section,
            .table-section {
                padding: 1.5rem;
            }

            .movie-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .admin-table {
                font-size: 0.8rem;
            }

            .movie-poster {
                width: 60px;
                height: 90px;
            }

            .alert-container {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include "../components/header.php"; ?>
    
    <div class="floating-shapes">
        <i class="fas fa-cogs shape"></i>
        <i class="fas fa-film shape"></i>
        <i class="fas fa-database shape"></i>
        <i class="fas fa-users-cog shape"></i>
    </div>

    <!-- Success/Error Alerts -->
    <?php if (!empty($success_message) || !empty($error_message)) { ?>
        <div class="alert-container">
            <?php if (!empty($success_message)) { ?>
                <div class="admin-alert admin-alert-success alert alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <?php if (!empty($error_message)) { ?>
                <div class="admin-alert admin-alert-danger alert alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="admin-container">
        <!-- Header Section -->
        <div class="admin-header">
            <h1 class="admin-title">
                <i class="fas fa-crown me-3"></i>
                Admin Dashboard
            </h1>
            <p class="admin-subtitle">Manage movies, reviews, and user content</p>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Form Section -->
            <div class="form-section">
                <div class="form-header">
                    <h2 class="form-title">
                        <i class="fas fa-<?= $edit_mode ? 'edit' : 'plus-circle' ?> me-2"></i>
                        <?= $edit_mode ? 'Edit Movie' : 'Add New Movie' ?>
                    </h2>
                    <p class="form-subtitle">
                        <?= $edit_mode ? 'Update movie information' : 'Fill in the details to add a new movie' ?>
                    </p>
                </div>

                <form method="post" id="movieForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="movie_id" value="<?= $edit_mode ? $movie_id : '' ?>">
                    
                    <div class="form-group">
                        <label for="title" class="form-label">
                            <i class="fas fa-film label-icon"></i>
                            Movie Title
                        </label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($title) ?>" required 
                               placeholder="Enter movie title">
                        <div class="form-text">The official title of the movie</div>
                        <div class="invalid-feedback">Please enter the movie's title.</div>
                    </div>

                    <div class="form-group">
                        <label for="genre" class="form-label">
                            <i class="fas fa-tags label-icon"></i>
                            Genre
                        </label>
                        <input type="text" class="form-control" id="genre" name="genre" 
                               value="<?= htmlspecialchars($genre) ?>" required 
                               placeholder="e.g., Action, Drama, Comedy">
                        <div class="form-text">Movie genre or category</div>
                        <div class="invalid-feedback">Please enter the genre.</div>
                    </div>

                    <div class="form-group">
                        <label for="release_date" class="form-label">
                            <i class="fas fa-calendar-alt label-icon"></i>
                            Release Date
                        </label>
                        <input type="date" class="form-control" id="release_date" name="release_date" 
                               value="<?= htmlspecialchars($release_date) ?>" required>
                        <div class="form-text">When the movie was released</div>
                        <div class="invalid-feedback">Please select the release date.</div>
                    </div>

                    <div class="form-group">
                        <label for="director" class="form-label">
                            <i class="fas fa-user-tie label-icon"></i>
                            Director
                        </label>
                        <input type="text" class="form-control" id="director" name="director" 
                               value="<?= htmlspecialchars($director) ?>" required 
                               placeholder="Director's name">
                        <div class="form-text">The movie's director</div>
                        <div class="invalid-feedback">Please enter the director's name.</div>
                    </div>

                    <div class="form-group">
                        <label for="classification" class="form-label">
                            <i class="fas fa-certificate label-icon"></i>
                            Classification
                        </label>
                        <input type="text" class="form-control" id="classification" name="classification" 
                               value="<?= htmlspecialchars($classification) ?>" required 
                               placeholder="e.g., PG, PG-13, R">
                        <div class="form-text">Movie rating classification</div>
                        <div class="invalid-feedback">Please enter the classification.</div>
                    </div>

                    <div class="form-group">
                        <label for="poster_upload" class="form-label">
                            <i class="fas fa-upload label-icon"></i>
                            Movie Poster
                        </label>
                        
                        <!-- Upload Option -->
                        <div class="poster-upload-section">
                            <input type="file" class="form-control" id="poster_upload" name="poster_upload" 
                                   accept="image/*" onchange="handlePosterUpload(this)">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Upload a poster image (JPG, PNG, GIF, WEBP - Max 5MB)
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="posterPreview" class="mt-3"></div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="submit" class="btn btn-custom btn-primary-custom">
                            <i class="fas fa-<?= $edit_mode ? 'save' : 'plus' ?>"></i>
                            <?= $edit_mode ? 'Update Movie' : 'Add Movie' ?>
                        </button>
                        <?php if ($edit_mode) { ?>
                            <a href="admin.php" class="btn btn-custom btn-secondary-custom">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        <?php } ?>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <div class="table-header">
                    <h2 class="table-title">
                        <i class="fas fa-list me-2"></i>
                        Movie Library
                    </h2>
                </div>

                <div class="movie-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?= count($movies) ?></div>
                        <div class="stat-label">Total Movies</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= count(array_unique(array_column($movies, 'genre'))) ?></div>
                        <div class="stat-label">Genres</div>
                    </div>
                </div>

                <?php if (empty($movies)) { ?>
                    <div class="empty-state">
                        <i class="fas fa-film"></i>
                        <h4>No Movies Found</h4>
                        <p>Start by adding your first movie to the library!</p>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Poster</th>
                                    <th>Title</th>
                                    <th>Genre</th>
                                    <th>Release Date</th>
                                    <th>Director</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movies as $movie) { ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" 
                                                 alt="<?= htmlspecialchars($movie['title']) ?>"
                                                 class="movie-poster"
                                                 onerror="this.src='../images/no-poster.jpg'">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($movie['title']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= htmlspecialchars($movie['genre']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars(date("M d, Y", strtotime($movie['release_date']))) ?></td>
                                        <td><?= htmlspecialchars($movie['director']) ?></td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($movie['classification']) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="admin.php?edit=<?= $movie['movie_id'] ?>" 
                                                   class="btn btn-action btn-edit"
                                                   title="Edit Movie">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-action btn-delete" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal" 
                                                        data-movie-id="<?= $movie['movie_id'] ?>"
                                                        data-movie-title="<?= htmlspecialchars($movie['title']) ?>"
                                                        title="Delete Movie">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete this movie?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Movie:</strong> <span id="movieTitle"></span><br>
                        <small>This action cannot be undone. All associated reviews will also be deleted.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-custom btn-secondary-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <a href="#" id="confirmDelete" class="btn btn-custom btn-delete">
                        <i class="fas fa-trash me-1"></i>
                        Delete Movie
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal functionality
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var movieId = button.getAttribute('data-movie-id');
                var movieTitle = button.getAttribute('data-movie-title');
                
                var confirmDeleteButton = deleteModal.querySelector('#confirmDelete');
                var movieTitleSpan = deleteModal.querySelector('#movieTitle');
                
                confirmDeleteButton.href = 'admin.php?delete=' + movieId;
                movieTitleSpan.textContent = movieTitle;
            });

            // Form validation
            var form = document.getElementById('movieForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
                
                // Add loading state
                var submitBtn = form.querySelector('button[type="submit"]');
                var originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                submitBtn.disabled = true;
                
                if (form.checkValidity()) {
                    // Allow form submission to continue
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                } else {
                    // Reset button if validation fails
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });

            // Auto-hide alerts after 5 seconds
            var alerts = document.querySelectorAll('.admin-alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Image upload and preview functionality
            window.handlePosterUpload = function(input) {
                var file = input.files[0];
                var preview = document.getElementById('posterPreview');
                
                if (file) {
                    // Clear URL input when file is selected
                    document.getElementById('poster_url_text').value = '';
                    
                    // Validate file type
                    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, GIF, WEBP)');
                        input.value = '';
                        preview.innerHTML = '';
                        return;
                    }
                    
                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        input.value = '';
                        preview.innerHTML = '';
                        return;
                    }
                    
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = 
                            '<div class="preview-container">' +
                            '<img src="' + e.target.result + '" alt="Poster Preview" class="preview-image">' +
                            '<div class="file-info">' +
                            '<i class="fas fa-file-image me-1"></i>' + file.name + 
                            ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)' +
                            '</div>' +
                            '</div>';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            };

            window.handleUrlInput = function(input) {
                var url = input.value.trim();
                var preview = document.getElementById('posterPreview');
                var fileInput = document.getElementById('poster_upload');
                
                if (url) {
                    // Clear file input when URL is entered
                    fileInput.value = '';
                    
                    preview.innerHTML = 
                        '<div class="preview-container">' +
                        '<img src="' + url + '" alt="Poster Preview" class="preview-image" ' +
                        'onerror="this.parentElement.innerHTML=\'<p class=text-danger><i class=fas fa-exclamation-triangle></i> Invalid image URL</p>\'">' +
                        '<div class="file-info">' +
                        '<i class="fas fa-link me-1"></i>External URL' +
                        '</div>' +
                        '</div>';
                } else {
                    preview.innerHTML = '';
                }
            };

            // Initialize preview if editing
            var urlInput = document.getElementById('poster_url_text');
            if (urlInput.value) {
                handleUrlInput(urlInput);
            }

            // Drag and drop functionality
            var uploadSection = document.querySelector('.poster-upload-section');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadSection.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                uploadSection.classList.add('dragover');
            }

            function unhighlight(e) {
                uploadSection.classList.remove('dragover');
            }

            uploadSection.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                var dt = e.dataTransfer;
                var files = dt.files;
                var fileInput = document.getElementById('poster_upload');
                fileInput.files = files;
                handlePosterUpload(fileInput);
            }

            // Poster URL preview functionality
            var posterInput = document.getElementById('poster_url');
            var previewContainer = document.createElement('div');
            previewContainer.className = 'mt-2';
            posterInput.parentNode.appendChild(previewContainer);

            function updatePosterPreview() {
                var url = posterInput.value.trim();
                if (url) {
                    previewContainer.innerHTML = 
                        '<div class="poster-preview">' +
                        '<label class="form-label text-muted">Preview:</label>' +
                        '<img src="' + url + '" alt="Poster Preview" ' +
                        'style="max-width: 100px; max-height: 150px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" ' +
                        'onerror="this.src=\'../images/no-poster.jpg\';">' +
                        '</div>';
                } else {
                    previewContainer.innerHTML = '';
                }
            }

            posterInput.addEventListener('input', updatePosterPreview);
            updatePosterPreview(); // Initial preview if editing

            // Character counters for text inputs
            var textInputs = ['title', 'genre', 'director', 'classification'];
            textInputs.forEach(function(inputId) {
                var input = document.getElementById(inputId);
                var counter = document.createElement('small');
                counter.className = 'text-muted';
                counter.style.float = 'right';
                counter.style.marginTop = '-1.5rem';
                counter.style.position = 'relative';
                counter.style.zIndex = '10';
                
                input.parentNode.appendChild(counter);
                
                function updateCounter() {
                    var maxLength = input.getAttribute('maxlength') || 100;
                    var remaining = maxLength - input.value.length;
                    counter.textContent = input.value.length + '/' + maxLength;
                    
                    if (remaining < 10) {
                        counter.className = 'text-danger';
                    } else if (remaining < 20) {
                        counter.className = 'text-warning';
                    } else {
                        counter.className = 'text-muted';
                    }
                }
                
                input.addEventListener('input', updateCounter);
                updateCounter();
            });

            // Table row animations
            var tableRows = document.querySelectorAll('.admin-table tbody tr');
            tableRows.forEach(function(row, index) {
                row.style.animationDelay = (index * 0.1) + 's';
                row.classList.add('fade-in-row');
            });

            // Add CSS for row animation
            var style = document.createElement('style');
            style.textContent = `
                .fade-in-row {
                    animation: fadeInRow 0.6s ease-out both;
                }
                @keyframes fadeInRow {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);

            // Smooth scroll to form when editing
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('edit')) {
                document.querySelector('.form-section').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>

    <?php include "../components/footer.php"; ?>
</body>
</html>