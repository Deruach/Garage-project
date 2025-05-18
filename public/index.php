<?php
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    // Verander \ naar /
    $classPath = str_replace('\\', '/', $class);

    // Pad naar de class
    $file = __DIR__ . '/../' . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        die("Kan de klasse niet vinden: " . $file);
    }
});


// Config laden
$config = require '../config/config.php';

// Initialiseer database en usermodel
use app\Core\Database;
use app\Models\User;
use app\Controllers\AuthController;

$db = new Database($config['db']);
$userModel = new User($db);
$auth = new AuthController($userModel);

// Verwerken van login- en registratieverzoeken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        if ($auth->login($_POST['email'], $_POST['password'])) {
            header("Location: dashboard.php"); // Redirect naar dashboard bij succes
            exit;
        } else {
            echo "Foutieve inloggegevens.";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        if ($auth->register($_POST['email'], $_POST['password'])) {
            echo "Account aangemaakt. Je kunt nu inloggen.";
        } else {
            echo "Gebruiker bestaat al.";
        }
    }
}
?>

<!-- HTML formulier voor inloggen en registreren -->
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Garage - Luris</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <!-- Navigatie -->
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="#"><div class="text-2xl font-bold text-blue-600">Luris Garage</div></a>
    <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Inloggen</a>
  </nav>

  <!-- Hero Sectie -->
  <section class="text-center py-20 px-4 bg-gradient-to-r from-blue-500 to-blue-700 text-white">
    <h1 class="text-4xl font-bold mb-4">Welkom bij Luris Garage</h1>
    <p class="text-xl">Uw betrouwbare partner voor onderhoud, reparatie en APK keuringen</p>
  </section>

  <!-- Over Ons -->
  <section class="py-16 px-6 max-w-4xl mx-auto text-center">
    <h2 class="text-3xl font-bold mb-4">Waarom kiezen voor ons?</h2>
    <p class="text-gray-700 mb-6">Onze ervaren monteurs staan klaar om uw auto in topconditie te houden. Met duidelijke communicatie en eerlijke prijzen zijn wij uw eerste keuze in auto-onderhoud.</p>
  </section>

  <!-- Footer -->
  <footer class="bg-white text-center py-4 border-t">
    <p class="text-sm text-gray-500">&copy; 2025 Luris Garage. Alle rechten voorbehouden.</p>
  </footer>
</body>
</html>
