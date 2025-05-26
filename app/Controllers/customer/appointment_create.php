<?php

use App\Controllers\AppointmentController;
use App\Models\Appointment;
use App\Models\Vehicle;

require_once '../../vendor/autoload.php';
require_once '../../config/database.php'; // waar je PDO aanmaakt

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getPDO(); // jouw database connectie
    $appointmentModel = new Appointment($db);
    $vehicleModel = new Vehicle($db);
    $controller = new AppointmentController($appointmentModel, $vehicleModel);

    $customerId = $_SESSION['user_id'];
    $licensePlate = $_POST['kenteken'];
    $date = $_POST['datum'];
    $note = $_POST['opmerkingen'] ?? '';

    $result = $controller->createAppointment($customerId, $licensePlate, $date, $note);

    if ($result['success'] ?? $result === true) {
        header("Location: ../../views/customer/afspraak_bevestigd.php");
        exit;
    } else {
        echo "Fout: " . ($result['message'] ?? 'Onbekende fout');
    }
}
