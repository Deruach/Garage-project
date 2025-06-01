<?php
session_start();

use App\Core\Database;
use App\Models\Appointment;
use App\Models\Vehicle;
use App\Models\Repair;
use App\Models\RepairType;
use App\Models\Part;

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mechanic') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Appointment.php';
require_once __DIR__ . '/../../app/Models/Vehicle.php';
require_once __DIR__ . '/../../app/Models/Repair.php';
require_once __DIR__ . '/../../app/Models/RepairType.php';
require_once __DIR__ . '/../../app/Models/Part.php';

$config = require __DIR__ . '/../../config/config.php';
$db = new Database($config['db']);

$appointmentModel = new Appointment($db);
$vehicleModel = new Vehicle($db);
$repairModel = new Repair($db);
$repairTypeModel = new RepairType($db);
$partModel = new Part($db);

$mechanicId = $_SESSION['user_id'];
$todaysAppointments = $appointmentModel->getByMechanicAndDate($mechanicId, date('Y-m-d'));
$repairTypes = $repairTypeModel->getAll();
$parts = $partModel->getAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Monteur Dashboard - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-gray-900 text-white py-4 px-6 flex justify-between items-center">
  <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div>
  <div class="flex items-center gap-6">
    <a href="../logout.php" class="text-sm text-yellow-400 hover:underline font-medium">Uitloggen</a>
  </div>
</nav>

<main class="p-6">
  <h1 class="text-3xl font-bold mb-6">Mijn taken voor vandaag (<?= date('d-m-Y') ?>)</h1>

  <?php if ($todaysAppointments): ?>
    <div class="space-y-8">
    <?php foreach ($todaysAppointments as $appt):
      $vehicle = $vehicleModel->getById($appt['vehicle_id']);
      $repair = $repairModel->getByAppointmentId($appt['id']);
    ?>
      <div class="bg-white p-6 rounded shadow border border-yellow-300">
        <h2 class="text-xl font-bold mb-2 text-gray-800">
          <?= htmlspecialchars($appt['customer_name']) ?> – <?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']) ?> (<?= htmlspecialchars($vehicle['license_plate']) ?>)
        </h2>
        <p class="text-sm text-gray-700 mb-4">Status: <strong><?= htmlspecialchars($repair['status']) ?></strong> | Afspraakdatum: <?= htmlspecialchars($appt['appointment_date']) ?></p>

        <form method="POST" action="update_repair.php" class="space-y-3">
          <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">

<!-- Handeling (alleen tonen, niet wijzigen) -->
<div>
  <label class="block text-sm font-medium text-gray-600">Handeling</label>
  <p class="bg-gray-100 border px-3 py-2 rounded text-gray-800">
    <?= htmlspecialchars($repairTypeModel->getById($repair['repair_type_id'])['description']) ?>
  </p>
</div>

<!-- Onderdeel toevoegen -->
<form method="POST" action="update_repair.php" class="mt-4 flex items-center gap-4">
  <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
  <select name="part_id" class="border px-3 py-1 rounded">
    <option value="">-- Kies onderdeel --</option>
    <?php foreach ($parts as $p): ?>
      <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (voorraad: <?= $p['stock'] ?>)</option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400">
    Voeg onderdeel toe
  </button>
</form>


<!-- Reparatie afronden -->
<form method="POST" action="update_repair.php" class="mt-2">
  <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
  <button type="submit" name="complete" value="1" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
    Reparatie afronden
  </button>
</form>

    <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-600">Je hebt vandaag geen taken gepland.</p>
  <?php endif; ?>
</main>
</body>
</html>