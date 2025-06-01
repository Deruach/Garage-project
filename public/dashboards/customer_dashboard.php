<?php
session_start();

use app\Core\Database;

// Zorg dat de browser geen pagina's cached zodat 'back' na logout niet werkt
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check of gebruiker is ingelogd én rol klopt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Review.php';

$config = require __DIR__ . '/../../config/config.php';

$dbInstance = new Database($config['db']);
$db = $dbInstance->getConnection();

$reviewModel = new \app\Models\Review($db);

// Haal de laatste 4 reviews op
$latestReviews = $reviewModel->getLatestReviews(4);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Luris Garage - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-gray-900 text-white py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div>
    <div class="flex items-center gap-6">
      <a href="customer/vehicles.php" class="text-yellow-500 hover:underline font-semibold">Mijn voertuigen beheren</a>
      <a href="customer/appointment.php" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 text-sm font-semibold shadow-sm">Afspraak maken</a>
      <a href="../logout.php" class="text-sm text-yellow-400 hover:underline font-medium">Uitloggen</a>
    </div>
  </nav>

  <!-- Welkom -->
  <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-20 px-4 text-center shadow-inner">
    <h1 class="text-4xl font-bold mb-2">Welkom terug, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
    <p class="text-lg opacity-90">Wat wil je vandaag doen?</p>
  </header>

  <!-- Waarom kiezen -->
  <section class="py-20 px-6 max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Waarom kiezen voor Luris Garage?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">🔧 Ervaren monteurs</h3>
        <p class="text-sm">Onze gecertificeerde monteurs werken met passie en precisie aan elk voertuig.</p>
      </div>
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">💬 Transparante communicatie</h3>
        <p class="text-sm">Altijd vooraf inzicht in de kosten, zonder verborgen verrassingen.</p>
      </div>
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">🚗 Snelle service</h3>
        <p class="text-sm">In veel gevallen kunt u uw auto dezelfde dag nog ophalen.</p>
      </div>
    </div>
  </section>

  <!-- Reviews -->
  <section class="max-w-5xl mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Wat klanten zeggen</h2>
    <div class="grid gap-6 md:grid-cols-2">
      <?php foreach ($latestReviews as $review): ?>
        <div class="bg-white p-6 rounded-lg shadow border border-yellow-300">
          <p class="text-yellow-500 text-lg mb-2">
            <?php for ($i = 0; $i < ($review['rating'] ?? 0); $i++) echo '⭐'; ?>
          </p>
          <p class="text-gray-700 italic">"<?php echo htmlspecialchars($review['comment'] ?? ''); ?>"</p>
          <div class="mt-4 text-sm text-gray-500">– <?php echo htmlspecialchars($review['name'] ?? ''); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Review formulier -->
  <section class="max-w-2xl mx-auto px-6 pt-4 pb-20">
    <?php if (!empty($_SESSION['review_success'])): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?php echo $_SESSION['review_success']; unset($_SESSION['review_success']); ?>
  </div>
<?php endif; ?>

<?php if (!empty($_SESSION['review_errors'])): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <ul class="list-disc ml-5">
      <?php foreach ($_SESSION['review_errors'] as $error): ?>
        <li><?php echo htmlspecialchars($error); ?></li>
      <?php endforeach; unset($_SESSION['review_errors']); ?>
    </ul>
  </div>
<?php endif; ?>

    <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Laat zelf een beoordeling achter</h2>
    <form action="../../reviews/submit.php" method="POST" class="bg-white p-6 rounded-lg shadow border border-yellow-300 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Beoordeling</label>
        <select name="rating" required class="w-full mt-1 px-4 py-2 border rounded-md">
          <option value="">-- Kies een score --</option>
          <option value="1">⭐ Slecht</option>
          <option value="2">⭐⭐ Matig</option>
          <option value="3">⭐⭐⭐ Redelijk</option>
          <option value="4">⭐⭐⭐⭐ Goed</option>
          <option value="5">⭐⭐⭐⭐⭐ Uitstekend</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Jouw review</label>
        <textarea rows="4" name="comment" required class="w-full mt-1 px-4 py-2 border rounded-md" placeholder="Vertel ons wat je van onze service vond..."></textarea>
      </div>
      <div>
        <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded font-semibold hover:bg-yellow-400 transition">Review plaatsen</button>
      </div>
    </form>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white text-center py-6 border-t text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

  <!-- Woordlimiet JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const textarea = document.querySelector("textarea");
      const maxWords = 30;
      const warning = document.createElement("p");
      warning.className = "text-sm text-gray-500 mt-1";
      textarea.parentNode.appendChild(warning);

      textarea.addEventListener("input", function () {
        const words = this.value.trim().split(/\s+/);
        if (words.length > maxWords) {
          this.value = words.slice(0, maxWords).join(" ");
          warning.textContent = `Maximaal ${maxWords} woorden toegestaan.`;
        } else {
          warning.textContent = `${words.length} / ${maxWords} woorden gebruikt.`;
        }
      });
    });
  </script>

</body>
</html>
