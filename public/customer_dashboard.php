<?php
session_start();
$config = require '../config/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GaragePro - Klant Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600 tracking-tight">GaragePro</div>
    <a href="logout.php" class="text-sm text-blue-600 hover:underline font-medium">Uitloggen</a>
  </nav>

  <!-- Welkomstsectie -->
  <header class="bg-gradient-to-r from-blue-500 to-blue-700 text-white py-20 px-4 text-center shadow-inner">
    <h1 class="text-4xl font-bold mb-2">  Welkom terug, <?= htmlspecialchars($_SESSION['name'] ?? 'Gebruiker'); ?>!</h1>
    <p class="text-lg opacity-90">Wat wil je vandaag doen?</p>
  </header>

  <!-- Actiekaarten -->
  <main class="max-w-5xl mx-auto px-6 py-16 grid gap-10 md:grid-cols-2">

    <!-- Afspraak maken -->
    <a href="afspraak_maken.php" class="bg-white hover:shadow-xl transition-shadow border rounded-2xl p-8 flex flex-col justify-between shadow-md">
      <div>
        <h2 class="text-2xl font-bold text-blue-600 mb-3">Afspraak maken</h2>
        <p class="text-gray-600 mb-4">Plan een onderhoudsbeurt, APK of andere service wanneer het jou uitkomt.</p>
      </div>
      <span class="text-sm text-blue-500 font-semibold">Nu plannen &rarr;</span>
    </a>

    <!-- Status bekijken -->
    <a href="status_bekijken.php" class="bg-white hover:shadow-xl transition-shadow border rounded-2xl p-8 flex flex-col justify-between shadow-md">
      <div>
        <h2 class="text-2xl font-bold text-green-600 mb-3">Status bekijken</h2>
        <p class="text-gray-600 mb-4">Bekijk de voortgang van je auto en lees eventuele opmerkingen van de garage.</p>
      </div>
      <span class="text-sm text-green-500 font-semibold">Bekijk status &rarr;</span>
    </a>

  </main>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
