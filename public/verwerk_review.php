<?php
session_start();
$config = require '../config/config.php';

// ➕ Maak de databaseverbinding aan
$conn = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['dbname']
);

// ➕ Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    die("Databaseverbinding mislukt: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = !empty($_POST['naam']) ? trim($_POST['naam']) : null;
    $score = isset($_POST['score']) ? (int)$_POST['score'] : null;
    $tekst = !empty($_POST['tekst']) ? trim($_POST['tekst']) : null;

    // ✅ Limiet controleren
    $woordTelling = str_word_count($tekst);
    if ($woordTelling > 30) {
        $_SESSION['review_error'] = "Je review mag maximaal 100 woorden bevatten. Je gebruikte er $woordTelling.";
        header("Location: customer_dashboard.php#review");
        exit;
    }

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
