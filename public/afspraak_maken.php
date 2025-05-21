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
  <title>Afspraak maken - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">GaragePro</div>
    <a href="customer_dashboard.php" class="text-blue-600 hover:underline font-medium">Home</a>
  </nav>

  <!-- Afspraakformulier -->
  <div class="max-w-xl mx-auto mt-12 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-blue-600 mb-6">Plan een afspraak</h2>
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
    
    <!-- Datum (linkerkant) -->
    <div>
      <label for="datum" class="block mb-2 font-medium">Kies een datum</label>
      <input type="text" id="datum" name="datum" required class="w-full px-6 py-2 border rounded-md">
    </div>

    <!-- Overige velden (rechterkant) -->
    <div>
      <!-- Kenteken -->
      <label for="kenteken" class="block mb-2 font-medium">Voer je kenteken in</label>
      <input type="text" id="kenteken" name="kenteken" required placeholder="XX-123-YY" class="w-full mb-4 px-4 py-2 border rounded-md uppercase">

      <!-- Standaardhandelingen -->
      <label for="handeling" class="block mb-2 font-medium">Selecteer werkzaamheden</label>
      <select id="handeling" name="handeling" required class="w-full mb-4 px-4 py-2 border rounded-md">
        <option value="">-- Kies een optie --</option>
        <option value="APK keuring">APK keuring</option>
        <option value="Olie verversen">Olie verversen</option>
        <option value="Remmen controleren">Remmen controleren</option>
        <option value="Banden vervangen">Banden vervangen</option>
        <option value="Grote beurt">Grote beurt</option>
      </select>

      <!-- Opmerkingen -->
      <label for="opmerkingen" class="block mb-2 font-medium">Opmerkingen (optioneel)</label>
      <textarea id="opmerkingen" name="opmerkingen" rows="4" class="w-full px-4 py-2 border rounded-md" placeholder="Bijvoorbeeld: vreemde geluiden bij het starten..."></textarea>
    </div>
  </div>

  <!-- Verstuur knop -->
  <div class="mt-6">
    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
      Afspraak aanvragen
    </button>
  </div>
</form>

  </div>

  <!-- Overzicht van afspraken -->
<div class="max-w-4xl mx-auto mt-16 mb-12 bg-white p-8 rounded-lg shadow-md">
  <h2 class="text-xl font-bold text-gray-800 mb-4">Mijn gemaakte afspraken</h2>

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
          <a href="factuur.php?id=<?= $afspraak['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Factuur</a>
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


</body>
</html>
