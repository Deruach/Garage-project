<?php
session_start();
$config = require '../config/config.php';

$conn = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['dbname']
);

if ($conn->connect_error) {
    die("Verbindingsfout: " . $conn->connect_error);
}

$afspraakId = $_POST['afspraak_id'] ?? null;
$monteurId = $_POST['mechanic_id'] ?? null;
$actie = $_POST['action'] ?? null;

if ($afspraakId && $actie === 'bevestig' && $monteurId) {
    $stmt = $conn->prepare("UPDATE appointments SET mechanic_id = ?, status = 'confirmed' WHERE id = ?");
    $stmt->bind_param("ii", $monteurId, $afspraakId);
    $stmt->execute();
    $stmt->close();
} elseif ($afspraakId && $actie === 'status') {
    $stmt = $conn->prepare("UPDATE appointments SET status = 'in_progress' WHERE id = ?");
    $stmt->bind_param("i", $afspraakId);
    $stmt->execute();
    $stmt->close();
} elseif ($afspraakId && $actie === 'Afgerond') {
    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
    $stmt->bind_param("i", $afspraakId);
    $stmt->execute();
    $stmt->close();
}

header("Location: receptionist_dashboard.php");
exit;
