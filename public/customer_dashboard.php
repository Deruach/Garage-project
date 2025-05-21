<?php
session_start();
$config = require '../config/config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli(
  $config['db']['host'],
  $config['db']['username'],
  $config['db']['password'],
  $config['db']['dbname']
);

if ($conn->connect_error) {
  die("Databaseverbinding mislukt: " . $conn->connect_error);
}

// Reviews ophalen
$reviews = [];
$result = $conn->query("SELECT naam, score, tekst FROM reviews ORDER BY datum DESC LIMIT 10");

while ($row = $result->fetch_assoc()) {
  $reviews[] = $row;
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
    <div class="flex items-center gap-6">
      <a href="afspraak_maken.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">Afspraak maken</a>
      <a href="logout.php" class="text-sm text-blue-600 hover:underline font-medium">Uitloggen</a>
    </div>
  </nav>

  <!-- Welkomstsectie -->
  <header class="bg-gradient-to-r from-blue-500 to-blue-700 text-white py-20 px-4 text-center shadow-inner">
    <h1 class="text-4xl font-bold mb-2">
      Welkom terug, <?= htmlspecialchars($_SESSION['name'] ?? 'Gebruiker'); ?>!
    </h1>
    <p class="text-lg opacity-90">Wat wil je vandaag doen?</p>
  </header>

  <!-- Reviews -->
  <section class="max-w-4xl mx-auto px-6 py-16">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Ervaringen van andere klanten</h2>

    <div class="grid gap-6 md:grid-cols-1">
      <?php if (empty($reviews)): ?>
  <p class="text-gray-500">Er zijn nog geen reviews geplaatst.</p>
<?php else: ?>
  <div class="grid gap-6 md:grid-cols-2">
    <?php foreach ($reviews as $review): ?>
      <div class="bg-white p-6 rounded-lg shadow border">
        <p class="text-yellow-500 text-lg mb-2">
          <?= str_repeat("⭐", $review['score']) ?>
        </p>
        <p class="text-gray-700 italic">"<?= htmlspecialchars($review['tekst']) ?>"</p>
        <div class="mt-4 text-sm text-gray-500">– <?= htmlspecialchars($review['naam'] ?? 'Anonieme klant') ?></div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

  </section>
  <!-- Review achterlaten -->
<section class="max-w-2xl mx-auto px-6 pt-8 pb-16">
  <h2 class="text-xl font-bold text-gray-800 mb-4">Laat een beoordeling achter</h2>
<?php if (!empty($_SESSION['review_success'])): ?>
  <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
    <?= $_SESSION['review_success']; unset($_SESSION['review_success']); ?>
  </div>
<?php elseif (!empty($_SESSION['review_error'])): ?>
  <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
    <?= $_SESSION['review_error']; unset($_SESSION['review_error']); ?>
  </div>
<?php endif; ?>

  <form action="verwerk_review.php" method="POST" class="bg-white p-6 rounded-lg shadow border space-y-4">

    <!-- Naam -->
    <div>
      <label for="naam" class="block text-sm font-medium text-gray-700">Je naam (optioneel)</label>
      <input type="text" id="naam" name="naam" class="w-full mt-1 px-4 py-2 border rounded-md" placeholder="Bijv. Jan de Vries">
    </div>

    <!-- Beoordeling -->
    <div>
      <label for="score" class="block text-sm font-medium text-gray-700">Beoordeling</label>
      <select id="score" name="score" required class="w-full mt-1 px-4 py-2 border rounded-md">
        <option value="">-- Kies een score --</option>
        <option value="1">⭐ Slecht (1)</option>
        <option value="2">⭐⭐ Matig (2)</option>
        <option value="3">⭐⭐⭐ Redelijk (3)</option>
        <option value="4">⭐⭐⭐⭐ Goed (4)</option>
        <option value="5">⭐⭐⭐⭐⭐ Uitstekend (5)</option>
      </select>
    </div>

    <!-- Tekst -->
    <div>
      <label for="tekst" class="block text-sm font-medium text-gray-700">Jouw review</label>
      <textarea id="tekst" name="tekst" rows="4" required class="w-full mt-1 px-4 py-2 border rounded-md" placeholder="Vertel ons wat je van onze service vond..."></textarea>
    </div>

    <!-- Knop -->
    <div>
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        Review plaatsen
      </button>
    </div>
  </form>
</section>


  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-6 border-t">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const tekstveld = document.getElementById("tekst");
    const maxWoorden = 30;
    const waarschuwing = document.createElement("p");
    waarschuwing.className = "text-sm text-gray-500 mt-1";
    tekstveld.parentNode.appendChild(waarschuwing);

    tekstveld.addEventListener("input", function () {
      const aantalWoorden = this.value.trim().split(/\s+/).length;
      if (aantalWoorden > maxWoorden) {
        waarschuwing.textContent = `Maximaal ${maxWoorden} woorden toegestaan. Je hebt er nu ${aantalWoorden}.`;
        this.value = this.value.trim().split(/\s+/).slice(0, maxWoorden).join(" ");
      } else {
        waarschuwing.textContent = `${aantalWoorden} / ${maxWoorden} woorden gebruikt.`;
      }
    });
  });
</script>

</body>
</html>
