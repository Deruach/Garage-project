<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
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
  die("Verbindingsfout: " . $conn->connect_error);
}

$factuur_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

// ✅ Haal de factuur op (alleen als hij 'verzonden' is en van deze klant)
$stmt = $conn->prepare("
  SELECT i.id, i.totaalbedrag, i.status, i.datum, u.name
  FROM invoices i
  JOIN appointments a ON i.appointment_id = a.id
  JOIN users u ON a.customer_id = u.id
  WHERE i.id = ? AND i.status = 'verzonden' AND u.id = ?
");
$stmt->bind_param("ii", $factuur_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$factuur = $result->fetch_assoc();
$stmt->close();

if (!$factuur) {
  die("Deze factuur is niet beschikbaar.");
}

// ✅ Haal factuurregels op
$stmt = $conn->prepare("SELECT omschrijving, bedrag FROM invoice_lines WHERE invoice_id = ?");
$stmt->bind_param("i", $factuur_id);
$stmt->execute();
$regels = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Factuur #<?= $factuur_id ?> - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <main class="max-w-3xl mx-auto p-8 mt-10 bg-white rounded-lg shadow">
    <h1 class="text-3xl font-bold text-blue-600 mb-4">Factuur #<?= $factuur_id ?></h1>
    <p class="text-sm text-gray-500 mb-2">Datum: <?= $factuur['datum'] ?></p>
    <p class="text-sm text-gray-500 mb-6">Klant: <?= htmlspecialchars($factuur['name']) ?></p>

    <table class="w-full table-auto text-sm mb-6">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left px-4 py-2">Omschrijving</th>
          <th class="text-right px-4 py-2">Bedrag</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($regel = $regels->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="px-4 py-2"><?= htmlspecialchars($regel['omschrijving']) ?></td>
          <td class="px-4 py-2 text-right">€ <?= number_format($regel['bedrag'], 2, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr class="font-bold">
          <td class="px-4 py-2 text-right">Totaal:</td>
          <td class="px-4 py-2 text-right text-green-600">€ <?= number_format($factuur['totaalbedrag'], 2, ',', '.') ?></td>
        </tr>
      </tfoot>
    </table>

    <form method="POST" action="betaal_factuur.php">
      <input type="hidden" name="factuur_id" value="<?= $factuur_id ?>">
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
        Simuleer betaling
      </button>
    </form>
  </main>
</body>
</html>
