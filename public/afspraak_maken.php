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
      <textarea id="opmerkingen" name="opmerkingen" rows="4" class="w-full mb-6 px-4 py-2 border rounded-md" placeholder="Bijvoorbeeld: vreemde geluiden bij het starten..."></textarea>

      <!-- Verstuur knop -->
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
        Afspraak aanvragen
      </button>
    </form>
  </div>

  <!-- Overzicht van afspraken (dummy-voorbeeld) -->
  <div class="max-w-4xl mx-auto mt-16 mb-12 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Mijn gemaakte afspraken</h2>

    <table class="w-full table-auto border-collapse text-left text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-4 py-2 border-b">Datum</th>
          <th class="px-4 py-2 border-b">Kenteken</th>
          <th class="px-4 py-2 border-b">Handeling</th>
          <th class="px-4 py-2 border-b">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="px-4 py-2 border-b">2025-05-24</td>
          <td class="px-4 py-2 border-b">XX-123-YY</td>
          <td class="px-4 py-2 border-b">Olie verversen</td>
          <td class="px-4 py-2 border-b text-yellow-600">In behandeling</td>
        </tr>
        <tr>
          <td class="px-4 py-2 border-b">2025-05-10</td>
          <td class="px-4 py-2 border-b">AB-987-CD</td>
          <td class="px-4 py-2 border-b">APK keuring</td>
          <td class="px-4 py-2 border-b text-green-600">Afgerond</td>
        </tr>
      </tbody>
    </table>
  </div>

</body>
</html>
