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
  <title>Luris Garage - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-gray-900 text-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div>
    <div class="flex items-center gap-6">
      <a href="customer/appointment.php" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 text-sm font-semibold shadow-sm">Afspraak maken</a>
      <a href="../logout.php" class="text-sm text-yellow-400 hover:underline font-medium">Uitloggen</a>
    </div>
  </nav>

  <!-- Welkom -->
  <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-20 px-4 text-center shadow-inner">
    <h1 class="text-4xl font-bold mb-2">Welkom terug, <?php echo htmlspecialchars($_SESSION['name']); ?></p></h1>
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
      <!-- Repeatable review block -->
      <div class="bg-white p-6 rounded-lg shadow border border-yellow-300">
        <p class="text-yellow-500 text-lg mb-2">⭐⭐⭐⭐⭐</p>
        <p class="text-gray-700 italic">"Fantastische service en vriendelijke mensen!"</p>
        <div class="mt-4 text-sm text-gray-500">– Jan de Vries</div>
      </div>
    </div>
  </section>

  <!-- Review formulier -->
  <section class="max-w-2xl mx-auto px-6 pt-4 pb-20">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Laat zelf een beoordeling achter</h2>
    <form class="bg-white p-6 rounded-lg shadow border border-yellow-300 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Je naam (optioneel)</label>
        <input type="text" class="w-full mt-1 px-4 py-2 border rounded-md" placeholder="Bijv. Jan de Vries">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Beoordeling</label>
        <select required class="w-full mt-1 px-4 py-2 border rounded-md">
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
        <textarea rows="4" required class="w-full mt-1 px-4 py-2 border rounded-md" placeholder="Vertel ons wat je van onze service vond..."></textarea>
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
