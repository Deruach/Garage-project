<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Afspraak maken - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
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

    <form method="POST" action="verwerk_afspraak.php">
      <!-- Datum -->
      <label for="datum" class="block mb-2 font-medium">Kies een datum</label>
      <input type="date" id="datum" name="datum" required class="w-full mb-4 px-4 py-2 border rounded-md">

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
      <textarea id="opmerkingen" name="opmerkingen" rows="4" class="w-full mb-6 px-4 py-2 border rounded-md" placeholder="Bijvoorbeeld: vreemde geluiden bij het starten..."></textarea>

      <!-- Verstuur knop -->
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
        Afspraak aanvragen
      </button>
    </form>
  </div>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 mt-12 mb-4">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
