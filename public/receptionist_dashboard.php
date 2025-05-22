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
    die("Fout bij verbinden: " . $conn->connect_error);
}

// Alle afspraken van vandaag ophalen
$stmt = $conn->prepare("
    SELECT a.id, a.appointment_date, a.notes, a.status, a.mechanic_id, u.name AS klant_naam 
    FROM appointments a
    JOIN users u ON a.customer_id = u.id
    ORDER BY a.appointment_date DESC
");
$stmt->execute();
$afspraken = $stmt->get_result();


// Alle monteurs ophalen
$monteursResult = $conn->query("SELECT id, name FROM users WHERE role = 'mechanic'");
$monteurs = $monteursResult->fetch_all(MYSQLI_ASSOC);

// Conceptfacturen ophalen
$facturen = $conn->query("
  SELECT i.id, i.total_amount, i.status, i.created_at AS datum, u.name AS klant
FROM invoices i
JOIN appointments a ON i.appointment_id = a.id
JOIN users u ON a.customer_id = u.id
WHERE i.status = 'concept'
ORDER BY i.created_at DESC
");

?>


<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Receptie - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">GaragePro Receptie</div>
    <a href="logout.php" class="text-blue-600 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Titel -->
  <header class="bg-blue-600 text-white py-12 text-center shadow-inner">
    <h1 class="text-3xl font-bold">Dagoverzicht & Afspraakbeheer</h1>
    <p class="text-sm text-blue-100 mt-2">Beheer afspraken, wijs monteurs toe en volg de voortgang</p>
  </header>

  <!-- Afsprakenlijst -->
  <main class="max-w-6xl mx-auto px-6 py-12">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ingeplande afspraken vandaag</h2>

    <table class="w-full table-auto text-sm border-collapse bg-white shadow rounded-lg">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border-b text-left">Datum</th>
          <th class="px-4 py-2 border-b text-left">Klant</th>
          <th class="px-4 py-2 border-b text-left">Kenteken</th>
          <th class="px-4 py-2 border-b text-left">Werkzaamheden</th>
          <th class="px-4 py-2 border-b text-left">Status</th>
          <th class="px-4 py-2 border-b text-left">Monteur</th>
          <th class="px-4 py-2 border-b text-left">Actie</th>
        </tr>
      </thead>
      <tbody>
<?php while ($afspraak = $afspraken->fetch_assoc()): 
  $lines = explode("\n", $afspraak['notes']);
  $kenteken = str_replace('Kenteken: ', '', $lines[0] ?? '');
  $handeling = str_replace('Handeling: ', '', $lines[1] ?? '');
?>
  <tr class="border-t">
    <td class="px-4 py-2"><?= htmlspecialchars($afspraak['appointment_date']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($afspraak['klant_naam']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($kenteken) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($handeling) ?></td>
    <td class="px-4 py-2 text-yellow-600"><?= htmlspecialchars($afspraak['status']) ?></td>
    <td class="px-4 py-2">
      <form action="update_afspraak.php" method="POST" class="flex gap-2 items-center">
        <input type="hidden" name="afspraak_id" value="<?= $afspraak['id'] ?>">
        <select name="mechanic_id" class="border rounded px-2 py-1 text-sm">
          <option value="">Selecteer</option>
          <?php foreach ($monteurs as $monteur): ?>
            <option value="<?= $monteur['id'] ?>" <?= $afspraak['mechanic_id'] == $monteur['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($monteur['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
    </td>
    <td class="px-4 py-2 space-x-2">
        <button type="submit" name="action" value="bevestig" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Bevestig</button>
        <button type="submit" name="action" value="status" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Status</button>
        <button type="submit" name="action" value="Afgerond" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Afgerond</button>
      </form>
    </td>
  </tr>
<?php endwhile; ?>
</tbody>

    </table>
    <h2 class="text-xl font-semibold text-gray-800 mt-16 mb-4">Facturen wachten op beoordeling</h2>

<?php if ($facturen->num_rows === 0): ?>
  <p class="text-gray-600">Er zijn momenteel geen conceptfacturen.</p>
<?php else: ?>
  <table class="w-full table-auto text-sm border-collapse bg-white shadow rounded-lg mb-12">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 border-b text-left">Factuurnr</th>
        <th class="px-4 py-2 border-b text-left">Klant</th>
        <th class="px-4 py-2 border-b text-left">Datum</th>
        <th class="px-4 py-2 border-b text-right">Bedrag</th>
        <th class="px-4 py-2 border-b text-center">Acties</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($factuur = $facturen->fetch_assoc()): ?>
      <tr class="border-t">
        <td class="px-4 py-2">#<?= $factuur['id'] ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($factuur['klant']) ?></td>
        <td class="px-4 py-2"><?= $factuur['datum'] ?></td>
        <td class="px-4 py-2 text-right">€<?= number_format($factuur['total_amount'], 2, ',', '.') ?></td>
        <td class="px-4 py-2 text-center">
          <form method="POST" action="verwerk_factuurstatus.php" class="inline-flex gap-2">
            <input type="hidden" name="factuur_id" value="<?= $factuur['id'] ?>">
            <button name="actie" value="goedkeuren" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Goedkeuren</button>
            <button name="actie" value="afkeuren" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Afkeuren</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

  </main>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
