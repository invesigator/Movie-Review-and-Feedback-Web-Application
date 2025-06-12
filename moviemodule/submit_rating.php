<?php
session_start();
include '../components/movie_database.php'; // Adjust the path as needed

$pdo = getDatabaseConnection1();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['action']) && $_POST['action'] == 'load_data') {
        $movie_id = $_GET['movie_id'] ?? 0;
        $user_id = $_SESSION['id'] ?? 0;

        $average_rating = 0;
        $total_review = 0;
        $five_star_review = 0;
        $four_star_review = 0;
        $three_star_review = 0;
        $two_star_review = 0;
        $one_star_review = 0;
        $total_user_rating = 0;
        $review_data = array();

        // Fetch all reviews for the given movie
        $stmt = $pdo->prepare("
            SELECT 
                c.*, 
                u.first_name, 
                u.last_name
            FROM 
                comments_ratings AS c 
            JOIN 
                users AS u 
            ON 
                c.user_id = u.id 
            WHERE 
                c.movie_id = :movie_id
        ");

        $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($result as $row) {
            $review_data[] = array(
                'feedback_id' => $row["feedback_id"],
                'user_id' => $row["user_id"],
                'user_name' => $row["first_name"] . ' ' . $row["last_name"],
                'user_review' => $row["comment"],
                'rating' => $row["rating"],
                'datetime' => $row["date_posted"]
            );

            switch ($row["rating"]) {
                case 5:
                    $five_star_review++;
                    break;
                case 4:
                    $four_star_review++;
                    break;
                case 3:
                    $three_star_review++;
                    break;
                case 2:
                    $two_star_review++;
                    break;
                case 1:
                    $one_star_review++;
                    break;
            }

            $total_review++;
            $total_user_rating += $row["rating"];
        }

        if ($total_review != 0) {
            $average_rating = $total_user_rating / $total_review;
        }

        $output = array(
            'average_rating' => number_format($average_rating, 1),
            'total_review' => $total_review,
            'five_star_review' => $five_star_review,
            'four_star_review' => $four_star_review,
            'three_star_review' => $three_star_review,
            'two_star_review' => $two_star_review,
            'one_star_review' => $one_star_review,
            'review_data' => $review_data
        );

        // echo '<pre>';
        // print_r($output);
        // echo '</pre>';

        echo json_encode($output);
        exit();
    }

    // Handle saving a new review
    if (isset($_POST['rating_data'])) {
        $movie_id = $_GET['movie_id'] ?? 0;
        $user_id = $_SESSION['id'] ?? 0;
        $user_review = $_POST["user_review"];
        $rating_data = $_POST["rating_data"];

        if ($movie_id == 0 || $user_id == 0 || empty($user_review) || $rating_data == 0) {
            echo "<script>alert('Invalid input data.'); window.history.back();</script>";
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO comments_ratings (movie_id, user_id, comment, rating, date_posted) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$movie_id, $user_id, $user_review, $rating_data]);

            echo "Your Review & Rating Successfully Submitted";
        } catch (PDOException $e) {
            echo "<script>alert('Error when inserting comment: " . $e->getMessage() . "'); window.history.back();</script>";
            exit;
        }
    }
} else {
    echo "This script only handles POST requests.";
}
?>
