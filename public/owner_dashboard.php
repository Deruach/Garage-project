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
  <title>Beheerder - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php"><div class="text-2xl font-bold text-blue-600">Luris Garage</div></a>
    <a href="logout.php" class="text-blue-600 hover:underline text-sm font-medium">Uitloggen</a>
  </nav>

  <!-- Welkomstsectie -->
  <header class="bg-gradient-to-r from-gray-800 to-blue-800 text-white py-20 text-center shadow-inner px-4">
    <h1 class="text-4xl font-bold mb-2">Welkom, Beheerder!</h1>
    <p class="text-lg text-gray-300">Bekijk hieronder een overzicht van de prestaties van je garage.</p>
  </header>

  <!-- Statistieken -->
  <section class="max-w-6xl mx-auto px-6 py-12 grid gap-8 md:grid-cols-2">
    <div class="bg-white p-6 rounded-lg shadow border text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Totale omzet (mei)</h2>
      <p class="text-3xl font-bold text-green-600">€12.850,00</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Reparaties uitgevoerd</h2>
      <p class="text-3xl font-bold text-blue-600">36</p>
    </div>
  </section>

  <!-- Grafiek Placeholder -->
  <section class="max-w-6xl mx-auto px-6 pb-16">
    <div class="bg-white p-6 rounded-lg shadow border">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Omzet afgelopen 6 maanden</h2>
      <div class="h-64 flex items-center justify-center text-gray-400 border border-dashed rounded">
        <!-- Hier kan later Chart.js komen -->
        [Grafiek Placeholder]
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
