<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mechanic') {
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
    die("Verbinding mislukt: " . $conn->connect_error);
}

$monteur_id = $_SESSION['user_id'];

// Afspraken ophalen voor deze monteur
$stmt = $conn->prepare("
  SELECT a.id, a.appointment_date, a.notes, a.status, u.name AS klant_naam
  FROM appointments a
  JOIN users u ON a.customer_id = u.id
  WHERE a.mechanic_id = ? 
    AND a.status IN ('pending', 'confirmed', 'in_progress')
  ORDER BY a.appointment_date ASC
");

$stmt->bind_param("i", $monteur_id);
$stmt->execute();
$afspraken = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Monteur - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen flex flex-col">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage Monteur</div>
    <a href="logout.php" class="text-yellow-400 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Welkom -->
  <header class="bg-yellow-500 text-black py-10 text-center shadow-inner px-4">
    <h1 class="text-3xl font-bold">Welkom, monteur</h1>
    <p class="text-sm text-gray-800 mt-1">Bekijk en werk jouw toegewezen afspraken af</p>
  </header>

  <!-- Inhoud -->
  <main class="flex-grow max-w-5xl mx-auto px-6 py-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-8">Jouw afspraken</h2>

    <?php while ($afspraak = $afspraken->fetch_assoc()): 
      $lines = explode("\n", $afspraak['notes']);
      $kenteken = str_replace('Kenteken: ', '', $lines[0] ?? '');
      $handeling = str_replace('Handeling: ', '', $lines[1] ?? '');
    ?>
    <div class="bg-white rounded-lg shadow-md p-6 border border-yellow-300 mb-6">
      <div class="flex justify-between items-center mb-4">
        <div>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($handeling) ?> – <?= htmlspecialchars($kenteken) ?></p>
          <p class="text-sm text-gray-500"><?= htmlspecialchars($afspraak['appointment_date']) ?> | Klant: <?= htmlspecialchars($afspraak['klant_naam']) ?></p>
        </div>
        <span class="text-sm text-yellow-600 font-medium"><?= ucfirst($afspraak['status']) ?></span>
      </div>

      <form action="verwerk_reparatie.php" method="POST" class="space-y-5">
        <input type="hidden" name="appointment_id" value="<?= $afspraak['id'] ?>">

        <!-- Handeling -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Uitgevoerde werkzaamheden</label>
          <select name="handeling_id" class="w-full mt-1 border border-yellow-200 rounded px-3 py-2 bg-gray-50" required>
            <option value="">-- Kies een handeling --</option>
            <option value="olie">Olie verversen</option>
            <option value="remmen">Remmen vervangen</option>
            <option value="banden">Banden vervangen</option>
            <option value="apk">APK keuring</option>
          </select>
        </div>

        <!-- Onderdelen -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Gebruikte onderdelen</label>
          <div class="space-y-2" id="onderdelen-container">
            <div class="flex gap-2">
              <input type="text" name="onderdeel_namen[]" placeholder="Naam" class="w-1/2 border border-yellow-200 rounded px-2 py-1" required>
              <input type="number" step="0.01" name="onderdeel_prijzen[]" placeholder="Prijs (€)" class="w-1/2 border border-yellow-200 rounded px-2 py-1" required>
              <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold">✕</button>
            </div>
          </div>
          <button type="button" onclick="voegOnderdeelToe()" class="mt-2 text-yellow-600 text-sm hover:underline">+ onderdeel toevoegen</button>
        </div>

        <!-- Opmerkingen -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Opmerkingen</label>
          <textarea name="opmerkingen" rows="3" class="w-full mt-1 border border-yellow-200 rounded px-3 py-2 bg-gray-50" placeholder="Bijvoorbeeld: remschijven waren zwaar versleten..."></textarea>
        </div>

        <!-- Actieknop -->
        <div class="flex justify-end">
          <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 font-semibold">
            Markeer als afgerond
          </button>
        </div>
      </form>
    </div>
    <?php endwhile; ?>
  </main>

  <!-- Footer -->
  <footer class="bg-[#1f2937] text-white text-center py-6 text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

  <script>
    function voegOnderdeelToe() {
      const container = document.getElementById('onderdelen-container');
      const nieuw = container.firstElementChild.cloneNode(true);
      nieuw.querySelectorAll('input').forEach(input => input.value = '');
      container.appendChild(nieuw);
    }
  </script>

</body>
</html>
