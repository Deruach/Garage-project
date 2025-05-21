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
  die("Verbindingsfout");
}

$factuur_id = $_POST['factuur_id'] ?? null;

if ($factuur_id) {
  $stmt = $conn->prepare("UPDATE invoices SET status = 'betaald' WHERE id = ?");
  $stmt->bind_param("i", $factuur_id);
  $stmt->execute();
  $stmt->close();
}

header("Location: customer_dashboard.php");
exit;
