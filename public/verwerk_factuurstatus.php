<?php
session_start();
$config = require '../config/config.php';

$conn = new mysqli(
  $config['db']['host'],
  $config['db']['username'],
  $config['db']['password'],
  $config['db']['dbname']
);

$factuur_id = $_POST['factuur_id'] ?? null;
$actie = $_POST['actie'] ?? null;

if ($factuur_id && in_array($actie, ['goedkeuren', 'afkeuren'])) {
  $status = $actie === 'goedkeuren' ? 'verzonden' : 'afgekeurd';
  $stmt = $conn->prepare("UPDATE invoices SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $factuur_id);
  $stmt->execute();
  $stmt->close();
}

header("Location: receptionist_dashboard.php");
exit;
