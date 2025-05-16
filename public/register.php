<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registreren - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    
<!-- Navigatiebalk -->
 <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold text-blue-600">Luris Garage</a>
  </nav>

  <!-- Inlogformulier -->
  <div class="flex items-center justify-center mt-10">
  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Account aanmaken</h2>

    <form method="POST" action="verwerk_registratie.php">
      <label for="naam" class="block mb-2 font-medium">Naam</label>
      <input type="text" id="naam" name="naam" required class="w-full px-4 py-2 border rounded-md mb-4">

      <label for="email" class="block mb-2 font-medium">E-mail</label>
      <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md mb-4">

      <label for="wachtwoord" class="block mb-2 font-medium">Wachtwoord</label>
      <input type="password" id="wachtwoord" name="wachtwoord" required class="w-full px-4 py-2 border rounded-md mb-6">

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Registreren</button>
    </form>

    <p class="text-sm text-center mt-4 text-gray-600">
      Al een account? <a href="login.php" class="text-blue-600 hover:underline">Log in</a>.
    </p>
  </div>
</div>

</body>
</html>
