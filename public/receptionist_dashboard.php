<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: index.php");
    exit;
}
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
        <tr class="border-t">
          <td class="px-4 py-2">21-05-2025</td>
          <td class="px-4 py-2">Fatima B.</td>
          <td class="px-4 py-2">XX-123-YY</td>
          <td class="px-4 py-2">Olie verversen</td>
          <td class="px-4 py-2 text-yellow-600">In behandeling</td>
          <td class="px-4 py-2">
            <select class="border rounded px-2 py-1 text-sm">
              <option value="">Selecteer</option>
              <option value="1">Jan</option>
              <option value="2">Lisa</option>
              <option value="3">Marco</option>
            </select>
          </td>
          <td class="px-4 py-2 space-x-2">
            <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Bevestig</button>
            <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Status</button>
          </td>
        </tr>
        <!-- Meer rijen kunnen hier worden gegenereerd met PHP -->
      </tbody>
    </table>
  </main>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
