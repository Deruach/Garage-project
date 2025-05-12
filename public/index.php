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
    <title>Login / Registreren</title>
</head>
<body>
    <h2>Inloggen</h2>
    <form action="" method="POST">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Wachtwoord">
        <input type="submit" value="Inloggen">
        <input type="hidden" name="action" value="login">
    </form>

    <h2>Registreren</h2>
    <form action="" method="POST">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Wachtwoord">
        <input type="submit" value="Registreren">
        <input type="hidden" name="action" value="register">
    </form>
</body>
</html>
