<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beheerder - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen flex flex-col">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</a>
    <a href="logout.php" class="text-yellow-400 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Welkom -->
  <header class="bg-yellow-500 text-black py-14 text-center shadow-inner px-4">
    <h1 class="text-4xl font-bold mb-2">Welkom, Beheerder!</h1>
    <p class="text-lg text-gray-800">Bekijk hieronder een overzicht van de prestaties van je garage.</p>
  </header>

  <!-- Statistieken -->
  <section class="flex-grow max-w-6xl mx-auto px-6 py-12 grid gap-8 md:grid-cols-2">
    <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300 text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Totale omzet (mei)</h2>
      <p class="text-3xl font-bold text-green-600">€12.850,00</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300 text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Reparaties uitgevoerd</h2>
      <p class="text-3xl font-bold text-blue-600">36</p>
    </div>
  </section>

  <!-- Grafiek -->
  <section class="max-w-6xl mx-auto px-6 pb-16">
    <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Omzet afgelopen 6 maanden</h2>
      <div class="h-64 flex items-center justify-center text-gray-400 border-2 border-dashed rounded">
        <!-- Hier kan later Chart.js komen -->
        [Grafiek Placeholder]
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[#1f2937] text-white text-center py-6 text-sm mt-auto">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

</body>
</html>
