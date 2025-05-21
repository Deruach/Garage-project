<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mechanic') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Monteur - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">GaragePro Monteur</div>
    <a href="logout.php" class="text-blue-600 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Welkom -->
  <header class="bg-blue-700 text-white py-12 text-center shadow-inner px-4">
    <h1 class="text-3xl font-bold">Welkom, monteur</h1>
    <p class="text-blue-200 mt-2">Bekijk en werk jouw toegewezen afspraken af</p>
  </header>

  <!-- Toegewezen afspraken -->
  <main class="max-w-5xl mx-auto px-6 py-12">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Jouw afspraken</h2>

    <!-- Afsprakenlijst -->
    <div class="bg-white rounded-lg shadow p-6 border mb-6">
      <div class="flex justify-between items-center mb-4">
        <div>
          <p class="text-lg font-semibold text-gray-700">Olie verversen – XX-123-YY</p>
          <p class="text-sm text-gray-500">21-05-2025 | Klant: Kevin D.</p>
        </div>
        <span class="text-sm text-yellow-600 font-medium">In behandeling</span>
      </div>

      <form action="#" method="POST" class="space-y-4">
        <!-- Standaardhandeling -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Uitgevoerde werkzaamheden</label>
          <select name="handeling" class="w-full mt-1 border rounded px-3 py-2">
            <option value="">-- Kies een handeling --</option>
            <option value="olie">Olie verversen</option>
            <option value="remmen">Remmen vervangen</option>
            <option value="banden">Banden vervangen</option>
            <option value="apk">APK keuring</option>
          </select>
        </div>

        <!-- Gebruikte onderdelen -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Gebruikte onderdelen</label>
          <input type="text" name="onderdelen" placeholder="Bijv. Oliefilter, Bougies" class="w-full mt-1 border rounded px-3 py-2">
        </div>

        <!-- Opmerkingen -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Opmerkingen</label>
          <textarea name="opmerkingen" rows="3" class="w-full mt-1 border rounded px-3 py-2" placeholder="Bijvoorbeeld: remschijven waren zwaar versleten..."></textarea>
        </div>

        <!-- Acties -->
        <div class="flex justify-end">
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Markeer als afgerond
          </button>
        </div>
      </form>
    </div>

    <!-- Herhaal bovenstaande blokken voor andere afspraken -->
  </main>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
