<?php
session_start();

// Alle sessievariabelen leegmaken
$_SESSION = [];

// Verwijder de sessie cookie als die bestaat
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Vernietig de sessie
session_destroy();

header("Location: index.php"); // Redirect naar loginpagina
exit;
