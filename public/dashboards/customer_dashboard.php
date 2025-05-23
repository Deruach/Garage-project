<?php
session_start();

// Zorg dat de browser geen pagina's cached zodat 'back' na logout niet werkt
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Check of gebruiker is ingelogd én rol klopt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}
?>

<h1>Welkom customer</h1>
<p>Je bent ingelogd als: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
<a href="../logout.php">Uitloggen</a>
