<?php
session_start();

// Config inladen en database verbinden
$config = require __DIR__ . '/../config/config.php';

$host = $config['db']['host'];
$dbname = $config['db']['dbname'];
$username = $config['db']['username'];
$password = $config['db']['password'];

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Ingelogde gebruiker controleren
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verwerking van het formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = $_POST['datum'] ?? null;
    $kenteken = $_POST['kenteken'] ?? null;
    $handeling = $_POST['handeling'] ?? null;
    $opmerkingen = $_POST['opmerkingen'] ?? '';

    if (!$datum || !$kenteken || !$handeling) {
        $_SESSION['afspraak_fout'] = "Vul alle verplichte velden in.";
    } else {
        $notes = "Kenteken: " . strtoupper($kenteken) . "\nHandeling: " . $handeling . "\nOpmerkingen: " . $opmerkingen;

        $stmt = $conn->prepare("INSERT INTO appointments (customer_id, appointment_date, notes) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $datum, $notes);

        if ($stmt->execute()) {
            $_SESSION['afspraak_succes'] = "Afspraak succesvol opgeslagen!";
        } else {
            $_SESSION['afspraak_fout'] = "Er ging iets mis bij het opslaan.";
        }

        $stmt->close();
    }

    header("Location: afspraak_maken.php");
    exit;
}
