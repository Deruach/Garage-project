<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Repair {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getByAppointmentId($appointmentId) {
        $sql = "SELECT * FROM repairs WHERE appointment_id = :appointment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':appointment_id' => $appointmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAsDone(int $repairId): bool {
        $sql = "UPDATE repairs SET status = 'done' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $repairId]);
    }
}
