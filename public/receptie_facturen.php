<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: index.php");
    exit;
}

$config = require '../config/config.php';
$conn = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['dbname']
);
if ($conn->connect_error) {
    die("Databasefout: " . $conn->connect_error);
}

// Haal alle conceptfacturen op
$sql = "
SELECT i.id AS factuur_id, i.total_amount, i.status, i.datum, u.name AS klant
FROM invoices i
JOIN appointments a ON i.appointment_id = a.id
JOIN users u ON a.customer_id = u.id
WHERE i.status = 'concept'
ORDER BY i.datum DESC
";
$facturen = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Factuurbeheer - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
  <h1 class="text-2xl font-bold text-blue-600">Facturen - Receptie</h1>
  <a href="receptie_dashboard.php" class="text-blue-600 hover:underline text-sm">← Terug</a>
</nav>

<main class="max-w-5xl mx-auto p-6">
  <h2 class="text-xl font-semibold mb-4">Conceptfacturen</h2>

  <?php if ($facturen->num_rows === 0): ?>
    <p class="text-gray-600">Geen conceptfacturen beschikbaar.</p>
  <?php else: ?>
    <table class="w-full bg-white shadow rounded border">
      <thead class="bg-gray-100 text-sm">
        <tr>
          <th class="text-left px-4 py-2">Factuurnr</th>
          <th class="text-left px-4 py-2">Klant</th>
          <th class="text-left px-4 py-2">Datum</th>
          <th class="text-right px-4 py-2">Bedrag</th>
          <th class="text-center px-4 py-2">Acties</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($factuur = $facturen->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="px-4 py-2">#<?= $factuur['factuur_id'] ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($factuur['klant']) ?></td>
          <td class="px-4 py-2"><?= $factuur['datum'] ?></td>
          <td class="px-4 py-2 text-right">€<?= number_format($factuur['total_amount'], 2, ',', '.') ?></td>
          <td class="px-4 py-2 text-center">
            <a href="factuur_receptie.php?id=<?= $factuur['factuur_id'] ?>" class="text-blue-600 hover:underline">Bekijk</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
