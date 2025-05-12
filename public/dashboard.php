<?php
session_start();

// Als de gebruiker niet is ingelogd, redirect naar loginpagina
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<h1>Welkom op het Dashboard</h1>
<p>Je bent ingelogd als: <?php echo $_SESSION['email']; ?></p>
<a href="logout.php">Uitloggen</a>
