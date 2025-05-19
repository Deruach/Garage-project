<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Status - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">GaragePro</div>
    <a href="customer_dashboard.php" class="text-blue-600 hover:underline font-medium">Home</a>
  </nav>

  <!-- Status sectie -->
  <div class="max-w-3xl mx-auto mt-12 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-blue-600 mb-6">Status van uw auto</h2>

    <!-- Voorbeeldgegevens -->
    <div class="mb-4">
      <p class="font-medium text-gray-700">Kenteken: <span class="font-normal">XX-123-YY</span></p>
      <p class="font-medium text-gray-700">Afspraakdatum: <span class="font-normal">21 mei 2025</span></p>
      <p class="font-medium text-gray-700">Status: <span class="font-normal text-yellow-600">In behandeling</span></p>
      <p class="font-medium text-gray-700">Opmerkingen: <span class="font-normal">Olie vervangen, remmen gecontroleerd</span></p>
      <p class="font-medium text-gray-700">Factuurstatus: <span class="font-normal text-red-600">Nog niet betaald</span></p>
    </div>

    <!-- Betalen knop (simulatie) -->
    <form action="#" method="POST">
      <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
        Factuur betalen
      </button>
    </form>
  </div>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 mt-12 mb-4">
    &copy; 2025 GaragePro. Alle rechten voorbehouden.
  </footer>

</body>
</html>
