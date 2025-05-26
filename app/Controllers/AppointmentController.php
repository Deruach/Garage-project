<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Vehicle;

class AppointmentController {
    private $appointmentModel;
    private $vehicleModel;

    public function __construct(Appointment $appointmentModel, Vehicle $vehicleModel) {
        $this->appointmentModel = $appointmentModel;
        $this->vehicleModel = $vehicleModel;
    }

    public function createAppointment($customerId, $licensePlate, $date, $note, $repairTypeId) {
        $vehicle = $this->vehicleModel->getVehicleByLicenseAndCustomer($licensePlate, $customerId);
        if (!$vehicle) {
            return ['success' => false, 'message' => 'Voertuig niet gevonden of niet van jou.'];
        }

        $success = $this->appointmentModel->create($customerId, $vehicle['id'], $date, $note, $repairTypeId);

        if ($success) {
            return ['success' => true, 'message' => 'Afspraak succesvol aangemaakt!'];
        } else {
            return ['success' => false, 'message' => 'Er ging iets mis bij het aanmaken van de afspraak.'];
        }
    }

    public function getBookedDates() {
        return $this->appointmentModel->getAllAppointmentDates();
    }

    public function getAppointmentsForCustomer($customerId) {
        return $this->appointmentModel->getByCustomerId($customerId);
    }
}
