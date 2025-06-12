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

    if ($isAdmin) {
        // Admins can delete any review
        $stmt = $pdo->prepare("SELECT movie_id FROM comments_ratings WHERE feedback_id = ?");
        $stmt->execute([$feedback_id]);
    } else {
        // Regular users can only delete their own reviews
        $stmt = $pdo->prepare("SELECT movie_id FROM comments_ratings WHERE feedback_id = ? AND user_id = ?");
        $stmt->execute([$feedback_id, $_SESSION['id']]);
    }

    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        echo "No review found or you don't have permission to delete this review.";
        exit;
    }

    // Delete the review
    $delete_stmt = $pdo->prepare("DELETE FROM comments_ratings WHERE feedback_id = ?");
    $delete_stmt->execute([$feedback_id]);

    header("Location: movie_details.php?movie_id=" . $review['movie_id']);
    exit;
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<?php 
// Only include header and footer if we haven't redirected
include '../components/header.php';
include "../components/footer.php"; 
?>