<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$config = require __DIR__ . '/../config/config.php';

$conn = new mysqli(
  $config['db']['host'],
  $config['db']['username'],
  $config['db']['password'],
  $config['db']['dbname']
);
// Bezet geraakte datums ophalen uit de database
$result = $conn->query("SELECT appointment_date FROM appointments");
$bezetteDatums = [];

while ($row = $result->fetch_assoc()) {
  $bezetteDatums[] = $row['appointment_date'];
}

if ($conn->connect_error) {
  die("Databaseverbinding mislukt: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Afspraken ophalen voor deze klant
$stmt = $conn->prepare("SELECT id, appointment_date, notes, status FROM appointments WHERE customer_id = ? ORDER BY appointment_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$afspraken = [];
while ($row = $result->fetch_assoc()) {
  // Extract kenteken en handeling uit notes
  $lines = explode("\n", $row['notes']);
  $kenteken = str_replace('Kenteken: ', '', $lines[0] ?? '');
  $handeling = str_replace('Handeling: ', '', $lines[1] ?? '');
  $afspraken[] = [
    'id' => $row['id'],
    'datum' => $row['appointment_date'],
    'kenteken' => $kenteken,
    'handeling' => $handeling,
    'status' => $row['status']
  ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Afspraak maken - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div>
    <a href="customer_dashboard.php" class="atext-yellow-400 hover:underline text-sm font-medium">Terug naar home</a>
  </nav>

  <!-- Afspraakformulier -->
  <div class="max-w-4xl mx-auto mt-12 bg-white p-8 rounded-lg shadow-md border border-yellow-300">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Plan een afspraak</h2>

    <?php if (!empty($_SESSION['afspraak_succes'])): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
        <?= $_SESSION['afspraak_succes']; unset($_SESSION['afspraak_succes']); ?>
      </div>
    <?php elseif (!empty($_SESSION['afspraak_fout'])): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
        <?= $_SESSION['afspraak_fout']; unset($_SESSION['afspraak_fout']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="verwerk_afspraak.php">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Datum -->
        <div>
          <label for="datum" class="block mb-2 font-medium text-gray-700">Kies een datum</label>
          <input type="text" id="datum" name="datum" required class="w-full px-6 py-2 border rounded-md">
        </div>

        <!-- Invoervelden -->
        <div>
          <label for="kenteken" class="block mb-2 font-medium text-gray-700">Kenteken</label>
          <input type="text" id="kenteken" name="kenteken" required placeholder="XX-123-YY" class="w-full mb-4 px-4 py-2 border rounded-md uppercase">

          <label for="handeling" class="block mb-2 font-medium text-gray-700">Selecteer werkzaamheden</label>
          <select id="handeling" name="handeling" required class="w-full mb-4 px-4 py-2 border rounded-md">
            <option value="">-- Kies een optie --</option>
            <option value="APK keuring">APK keuring</option>
            <option value="Olie verversen">Olie verversen</option>
            <option value="Remmen controleren">Remmen controleren</option>
            <option value="Banden vervangen">Banden vervangen</option>
            <option value="Grote beurt">Grote beurt</option>
          </select>

          <label for="opmerkingen" class="block mb-2 font-medium text-gray-700">Opmerkingen (optioneel)</label>
          <textarea id="opmerkingen" name="opmerkingen" rows="4" class="w-full px-4 py-2 border rounded-md" placeholder="Bijvoorbeeld: vreemde geluiden bij het starten..."></textarea>
        </div>
      </div>

      <!-- Verstuurknop -->
      <div class="mt-6">
        <button type="submit" class="w-full bg-yellow-500 text-black py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
          Afspraak aanvragen
        </button>
      </div>
    </form>
  </div>

  <!-- Afsprakenoverzicht -->
  <div class="max-w-5xl mx-auto mt-16 mb-12 bg-white p-8 rounded-lg shadow-md border border-yellow-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Mijn gemaakte afspraken</h2>

    <table class="w-full table-auto border-collapse text-left text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-4 py-2 border-b">Datum</th>
          <th class="px-4 py-2 border-b">Kenteken</th>
          <th class="px-4 py-2 border-b">Handeling</th>
          <th class="px-4 py-2 border-b">Status</th>
          <th class="px-4 py-2 border-b">Actie</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($afspraken)): ?>
          <tr>
            <td colspan="5" class="text-center text-gray-500 py-4">Je hebt nog geen afspraken gepland.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($afspraken as $afspraak): ?>
            <tr>
              <td class="px-4 py-2 border-b"><?= htmlspecialchars($afspraak['datum']) ?></td>
              <td class="px-4 py-2 border-b"><?= htmlspecialchars($afspraak['kenteken']) ?></td>
              <td class="px-4 py-2 border-b"><?= htmlspecialchars($afspraak['handeling']) ?></td>
              <td class="px-4 py-2 border-b <?= $afspraak['status'] === 'afgerond' ? 'text-green-600' : 'text-yellow-600' ?>">
                <?= ucfirst($afspraak['status']) ?>
              </td>
              <td class="px-4 py-2 border-b">
                <a href="factuur.php?id=<?= $afspraak['id'] ?>" class="bg-yellow-500 text-black px-3 py-1 rounded hover:bg-yellow-400 text-sm font-semibold">Factuur</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Flatpickr JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    const bezetteDatums = <?= json_encode($bezetteDatums); ?>;
    flatpickr("#datum", {
      inline: true,
      dateFormat: "Y-m-d",
      minDate: "today",
      disable: bezetteDatums
    });
  </script>

  <!-- Footer -->
  <footer class="bg-[#1f2937] text-white text-center py-6 border-t text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

</body>
</html>
