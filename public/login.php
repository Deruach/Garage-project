<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - GaragePro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navigatiebalk -->
 <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">Luris Garage</div>
    <a href="index.php" class="text-blue-600 hover:underline font-medium">Home</a>
  </nav>

  <!-- Inlogformulier -->
  <div class="flex items-center justify-center mt-10">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
      <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Inloggen bij Luris Garage</h2>

      <form method="POST" action="verwerk_login.php">
        <label for="email" class="block mb-2 font-medium">E-mail</label>
        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md mb-4">

        <label for="wachtwoord" class="block mb-2 font-medium">Wachtwoord</label>
        <input type="password" id="wachtwoord" name="wachtwoord" required class="w-full px-4 py-2 border rounded-md mb-6">

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Login</button>
      </form>

      <p class="text-sm text-center mt-4 text-gray-600">
        Nog geen account? <a href="register.php" class="text-blue-600 hover:underline">Registreer hier</a>.
      </p>
    </div>
  </div>

</body>
</html>
