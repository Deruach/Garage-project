<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
  </style>
</head>
<body class="bg-[#f7f4f0] text-gray-800">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="#"><div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div></a>
    <a href="login.php" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 transition font-semibold shadow-sm">
      Inloggen
    </a>
  </nav>

  <!-- Hero -->
  <section class="text-center py-24 px-6 bg-gradient-to-br from-gray-800 to-gray-900 text-white">
    <h1 class="text-5xl font-black mb-4 drop-shadow-md">Jouw Auto. Onze Zorg.</h1>
    <p class="text-xl text-gray-200">Betrouwbare service, eerlijke prijzen en echte vakmensen sinds 1999.</p>
  </section>

  <!-- Over ons -->
  <section class="py-20 px-6 max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Waarom kiezen voor Luris Garage?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">🔧 Ervaren monteurs</h3>
        <p class="text-sm">Onze gecertificeerde monteurs werken met passie en precisie aan elk voertuig.</p>
      </div>
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">💬 Transparante communicatie</h3>
        <p class="text-sm">U krijgt altijd vooraf inzicht in de kosten, zonder verborgen verrassingen.</p>
      </div>
      <div class="bg-white p-6 rounded-lg border border-yellow-400 shadow hover:shadow-md transition">
        <h3 class="font-bold text-lg text-yellow-600 mb-2">🚗 Snelle service</h3>
        <p class="text-sm">In veel gevallen kunt u uw auto dezelfde dag nog ophalen.</p>
      </div>
    </div>
  </section>

  <!-- Reviews -->
  <section class="bg-white py-20">
    <div class="max-w-5xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold text-gray-800 mb-8">Wat klanten zeggen</h2>
      <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-gray-50 p-6 rounded-lg shadow border border-yellow-300">
          <p class="text-yellow-500 text-lg mb-2">★★★★★</p>
          <p class="text-gray-700 italic">"Superservice. Binnen een dag klaar en perfect geholpen."</p>
          <div class="mt-4 text-sm text-gray-500">– Fatima B.</div>
        </div>
        <div class="bg-gray-50 p-6 rounded-lg shadow border border-yellow-300">
          <p class="text-yellow-500 text-lg mb-2">★★★★☆</p>
          <p class="text-gray-700 italic">"Betrouwbaar en netjes. Ik kom hier zeker terug."</p>
          <div class="mt-4 text-sm text-gray-500">– Kevin D.</div>
        </div>
        <div class="bg-gray-50 p-6 rounded-lg shadow border border-yellow-300">
          <p class="text-yellow-500 text-lg mb-2">★★★★★</p>
          <p class="text-gray-700 italic">"Altijd vriendelijk, altijd eerlijk. Echt top!"</p>
          <div class="mt-4 text-sm text-gray-500">– Sandra V.</div>
        </div>
        <div class="bg-gray-50 p-6 rounded-lg shadow border border-yellow-300">
          <p class="text-yellow-500 text-lg mb-2">★★★★☆</p>
          <p class="text-gray-700 italic">"Snelle diagnose en duidelijke uitleg. Aanrader!"</p>
          <div class="mt-4 text-sm text-gray-500">– Willem P.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[#1f2937] text-white text-center py-6 text-sm mt-12">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

</body>
</html>
 