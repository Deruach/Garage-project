<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: login.php");
    exit;
}
?>

<h1>Welkom receptie</h1>
<p>Je bent ingelogd als: <?php echo $_SESSION['email']; ?></p>
<a href="logout.php">Uitloggen</a>