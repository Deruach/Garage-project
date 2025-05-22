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
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        if ($auth->login($_POST['email'], $_POST['password'])) {
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
            }
            exit;
        } else {
            $error = "Foutieve inloggegevens.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - Luris</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="index.php"><div class="text-2xl font-bold text-blue-600">Luris Garage</div></a>
  </nav>
  <div class="flex items-center justify-center" style="height: calc(100vh - 64px);">
    <div class="bg-white p-8 rounded-lg drop-shadow-lg shadow-2xl w-full max-w-sm">
      <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Inloggen bij </br> Luris Garage</h2>
      <form method="POST">
        <label for="email" class="block mb-2 font-medium">E-mail</label>
        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md mb-4">

        <label for="wachtwoord" class="block mb-2 font-medium">Wachtwoord</label>
        <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-md mb-6">
        <input type="hidden" name="action" value="login">

        <button type="submit" value="Inloggen" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Login</button>
        <?php if (!empty($error)): ?>
          <p class="text-red-600 text-sm mt-4 text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

      </form>

      <p class="text-sm text-center mt-4 text-gray-600">
        Nog geen account? <a href="register.php" class="text-blue-600 hover:underline">Registreer hier</a>.
      </p>
    </div>
  </div>
</body>
</html>
