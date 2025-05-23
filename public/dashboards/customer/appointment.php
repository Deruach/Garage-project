<?php
session_start();

// Zorg dat de browser geen pagina's cached zodat 'back' na logout niet werkt
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Check of gebruiker is ingelogd én rol klopt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}
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
    <a href="../customer_dashboard.php"><div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div></a>
  </nav>

  <!-- Afspraakformulier -->
  <div class="max-w-4xl mx-auto mt-12 bg-white p-8 rounded-lg shadow-md border border-yellow-300">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Plan een afspraak</h2>

    <form method="POST" novalidate>
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
        <tr>
          <td colspan="5" class="text-center text-gray-500 py-4">Je hebt nog geen afspraken gepland.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Flatpickr JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    const bezetteDatums = []; // Voeg bezette datums toe als JavaScript-array
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

