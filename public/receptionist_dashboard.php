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

// Alle afspraken ophalen
$stmt = $conn->prepare("
    SELECT a.id, a.appointment_date, a.notes, a.status, a.mechanic_id, u.name AS klant_naam 
    FROM appointments a
    JOIN users u ON a.customer_id = u.id
    ORDER BY a.appointment_date DESC
");
$stmt->execute();
$afspraken = $stmt->get_result();

// Monteurs ophalen
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
  <title>Receptie - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen flex flex-col">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage Receptie</div>
    <a href="logout.php" class="text-yellow-400 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Header -->
  <header class="bg-yellow-500 text-black py-10 text-center shadow-inner">
    <h1 class="text-3xl font-bold">Dagoverzicht & Afspraakbeheer</h1>
    <p class="text-sm text-gray-800 mt-1">Beheer afspraken, wijs monteurs toe en volg de voortgang</p>
  </header>

  <!-- Inhoud -->
  <main class="flex-grow max-w-6xl mx-auto px-6 py-12 space-y-16">

    <!-- Afspraken -->
    <section class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Ingeplande afspraken vandaag</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border-collapse">
          <thead class="bg-yellow-100 text-gray-700">
            <tr>
              <th class="px-4 py-2">Datum</th>
              <th class="px-4 py-2">Klant</th>
              <th class="px-4 py-2">Kenteken</th>
              <th class="px-4 py-2">Werkzaamheden</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Monteur</th>
              <th class="px-4 py-2">Actie</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php while ($afspraak = $afspraken->fetch_assoc()):
              $lines = explode("\n", $afspraak['notes']);
              $kenteken = str_replace('Kenteken: ', '', $lines[0] ?? '');
              $handeling = str_replace('Handeling: ', '', $lines[1] ?? '');
            ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($afspraak['appointment_date']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($afspraak['klant_naam']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($kenteken) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($handeling) ?></td>
                <td class="px-4 py-2 <?= $afspraak['status'] === 'afgerond' ? 'text-green-600' : 'text-yellow-600' ?>">
                  <?= htmlspecialchars($afspraak['status']) ?>
                </td>
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
                <td class="px-4 py-2 space-x-1">
                  <button type="submit" name="action" value="bevestig" class="bg-yellow-500 text-black px-3 py-1 rounded hover:bg-yellow-400 font-semibold">Bevestig</button>
                  <button type="submit" name="action" value="status" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 font-semibold">Status</button>
                  <button type="submit" name="action" value="Afgerond" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 font-semibold">Afgerond</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Facturen -->
    <section class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Facturen wachten op beoordeling</h2>

      <?php if ($facturen->num_rows === 0): ?>
        <p class="text-gray-600">Er zijn momenteel geen conceptfacturen.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left border-collapse">
            <thead class="bg-yellow-100 text-gray-700">
              <tr>
                <th class="px-4 py-2">Factuurnr</th>
                <th class="px-4 py-2">Klant</th>
                <th class="px-4 py-2">Datum</th>
                <th class="px-4 py-2 text-right">Bedrag</th>
                <th class="px-4 py-2 text-center">Acties</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php while ($factuur = $facturen->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
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
        </div>
      <?php endif; ?>
    </section>

  </main>

  <!-- Footer altijd onderaan -->
  <footer class="bg-[#1f2937] text-white text-center py-6 text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

</body>
</html>
