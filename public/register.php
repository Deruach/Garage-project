<?php
ob_start();
session_start();

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../' . $classPath . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$config = require '../config/config.php';

use app\Core\Database;
use app\Models\User;
use app\Controllers\AuthController;

$db = new Database($config['db']);
$userModel = new User($db);
$auth = new AuthController($userModel);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        // Registreren + meteen inloggen
        if ($auth->register($_POST['name'], $_POST['email'], $_POST['password'])) {
            // Login meteen na registratie
            if ($auth->login($_POST['email'], $_POST['password'])) {
                // Redirect naar rol-dashboard
                $role = $_SESSION['role'];
                switch ($role) {
                    case 'customer':
                        header("Location: dashboards/customer_dashboard.php");
                        break;
                    case 'receptionist':
                        header("Location: dashboards/receptionist_dashboard.php");
                        break;
                    case 'mechanic':
                        header("Location: dashboards/monteur_dashboard.php");
                        break;
                    case 'owner':
                        header("Location: dashboards/owner_dashboard.php");
                        break;
                    default:
                        $error = "Onbekende rol.";
                        break;
                }
                exit;
            }
        } else {
            $error = "Gebruiker bestaat al.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Registreren - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</a>
  </nav>

  <!-- Registratieformulier -->
  <div class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4">
    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-sm border border-yellow-300">
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Registreren bij <br><span class="text-yellow-500">Luris Garage</span></h2>

      <form method="POST">
        <label for="name" class="block mb-2 font-medium text-sm text-gray-700">Naam</label>
        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-md mb-4 focus:outline-yellow-400" />

        <label for="email" class="block mb-2 font-medium text-sm text-gray-700">E-mail</label>
        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md mb-4 focus:outline-yellow-400" />

        <label for="password" class="block mb-2 font-medium text-sm text-gray-700">Wachtwoord</label>
        <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-md mb-6 focus:outline-yellow-400" />

        <input type="hidden" name="action" value="register" />

        <button type="submit" class="w-full bg-yellow-500 text-black font-semibold py-2 rounded-md hover:bg-yellow-400 transition">
          Registreren
        </button>
      </form>

      <?php if ($error): ?>
        <p class="mt-4 text-red-600 text-center text-sm"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <p class="text-sm text-center mt-4 text-gray-600">
      All een account? <a href="login.php" class="text-yellow-600 font-medium hover:underline">Login hier</a>.
      </p>
    </div>
  </div>

</body>
</html>
