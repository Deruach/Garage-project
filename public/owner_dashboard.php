<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
?>

<h1>Welkom owner</h1>
<p>Je bent ingelogd als: <?php echo $_SESSION['email']; ?></p>
<a href="logout.php">Uitloggen</a>