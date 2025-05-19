
<?php
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
                        header("Location: customer_dashboard.php");
                        break;
                    case 'receptionist':
                        header("Location: receptionist_dashboard.php");
                        break;
                    case 'mechanic':
                        header("Location: monteur_dashboard.php");
                        break;
                    case 'owner':
                        header("Location: owner_dashboard.php");
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
    <title>Registreren - Luris</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php"><div class="text-2xl font-bold text-blue-600">Luris Garage</div></a>
  </nav>
  <div class="flex items-center justify-center" style="height: calc(100vh - 64px);">
    <div class="bg-white p-8 rounded-lg drop-shadow-lg shadow-2xl w-full max-w-sm">
      <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Registreren bij </br> Luris Garage</h2>
      <form method="POST">
        <label for="name" class="block mb-2 font-medium">Naam</label>
        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-md mb-4" />

        <label for="email" class="block mb-2 font-medium">E-mail</label>
        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md mb-4" />

        <label for="password" class="block mb-2 font-medium">Wachtwoord</label>
        <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-md mb-6" />

        <input type="hidden" name="action" value="register" />

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Registreren</button>
      </form>

      <?php if ($error): ?>
        <p class="mt-4 text-red-600 text-center text-sm"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <?php if ($success): ?>
        <p class="mt-4 text-green-600 text-center text-sm"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>
    </div>
  </div>
  
</body>
</html>
