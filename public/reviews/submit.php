<?php
session_start();

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Review.php';

use app\Core\Database;
use app\Models\Review;

$config = require __DIR__ . '/../../config/config.php';

$dbInstance = new Database($config['db']);
$db = $dbInstance->getConnection();

$reviewModel = new Review($db);
// Input ophalen en valideren
$customerId = $_SESSION['user_id'];
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Beperk aantal woorden in comment op server-side ook (zoals op client)
$maxWords = 30;
$wordCount = str_word_count($comment);
if ($wordCount > $maxWords) {
    $commentWords = preg_split('/\s+/', $comment);
    $comment = implode(' ', array_slice($commentWords, 0, $maxWords));
}

$errors = [];

// Validatie
if (!$rating || $rating < 1 || $rating > 5) {
    $errors[] = "Ongeldige beoordeling.";
}

if (empty($comment)) {
    $errors[] = "Review mag niet leeg zijn.";
}

// Bij fouten terugsturen
if (!empty($errors)) {
    $_SESSION['review_errors'] = $errors;
    header("Location: ../dashboards/customer_dashboard.php");
    exit;
}

// Review opslaan
$success = $reviewModel->addReview($customerId, $rating, $comment);

if ($success) {
    $_SESSION['review_success'] = "Bedankt voor je review!";
} else {
    $_SESSION['review_errors'] = ["Er is iets misgegaan bij het opslaan van je review."];
}

header("Location: ../dashboards/customer_dashboard.php");
exit;
