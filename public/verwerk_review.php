<?php
session_start();
require '../config/db_connect.php'; // Zorg dat dit klopt met jouw structuur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = !empty($_POST['naam']) ? trim($_POST['naam']) : null;
    $score = isset($_POST['score']) ? (int)$_POST['score'] : null;
    $tekst = !empty($_POST['tekst']) ? trim($_POST['tekst']) : null;

    if ($score >= 1 && $score <= 5 && $tekst) {
        $stmt = $conn->prepare("INSERT INTO reviews (naam, score, tekst) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $naam, $score, $tekst);
        if ($stmt->execute()) {
            $_SESSION['review_success'] = "Bedankt voor je beoordeling!";
        } else {
            $_SESSION['review_error'] = "Er ging iets mis bij het opslaan.";
        }
        $stmt->close();
    } else {
        $_SESSION['review_error'] = "Vul alle verplichte velden correct in.";
    }

    header("Location: customer_dashboard.php#review");
    exit;
}
