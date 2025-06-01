<?php
session_start();

use App\Core\Database;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Models\Appointment;

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check of monteur is ingelogd
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mechanic') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Repair.php';
require_once __DIR__ . '/../../app/Models/RepairPart.php';
require_once __DIR__ . '/../../app/Models/Appointment.php';

$config = require __DIR__ . '/../../config/config.php';
$db = new Database($config['db']);

$repairModel = new Repair($db);
$repairPartModel = new RepairPart($db);
$appointmentModel = new Appointment($db);

// Ophalen formulierdata
$appointmentId = $_POST['appointment_id'] ?? null;
$partId = $_POST['part_id'] ?? null;
$complete = isset($_POST['complete']);

if (!$appointmentId) {
    header('Location: monteur_dashboard.php');
    exit;
}

// Reparatie ophalen bij afspraak
$repair = $repairModel->getByAppointmentId($appointmentId);

if (!$repair) {
    header('Location: monteur_dashboard.php');
    exit;
}

// Onderdelen toevoegen
if (!empty($partId)) {
    $repairPartModel->addPart($repair['id'], (int)$partId);
}

// Reparatie afronden
if ($complete) {
    $repairModel->markAsDone($repair['id']);
    $appointmentModel->updateStatus($appointmentId, 'ready');
}

header('Location: monteur_dashboard.php');
exit;
