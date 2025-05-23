<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Appointment {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    // Maak een afspraak aan
    public function create(int $customerId, int $vehicleId, string $datum, string $opmerkingen, int $repairTypeId): bool {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO appointments (customer_id, vehicle_id, appointment_date, notes, status)
                    VALUES (:customer_id, :vehicle_id, :appointment_date, :notes, 'pending')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':appointment_date' => $datum,
                ':notes' => $opmerkingen
            ]);

            $appointmentId = $this->db->lastInsertId();

            $sqlRepair = "INSERT INTO repairs (appointment_id, repair_type_id, status) 
                          VALUES (:appointment_id, :repair_type_id, 'planned')";
            $stmtRepair = $this->db->prepare($sqlRepair);
            $stmtRepair->execute([
                ':appointment_id' => $appointmentId,
                ':repair_type_id' => $repairTypeId
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Tel het aantal afspraken op een bepaalde datum
    public function countAppointmentsByDate(string $datum): int {
        $sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = :datum";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':datum' => $datum]);
        return (int) $stmt->fetchColumn();
    }

    // Controleer of datum beschikbaar is (max 4 afspraken)
    public function isDateAvailable($datum) {
        $sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = :datum";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':datum' => $datum]);
        $count = (int)$stmt->fetchColumn();

        // Max 4 afspraken per dag toegestaan
        return $count < 4;
    }
    public function getFullAppointmentDates() {
        $sql = "
            SELECT appointment_date
            FROM appointments
            GROUP BY appointment_date
            HAVING COUNT(*) >= 4
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Haal alle afspraken van een klant op
    public function getByCustomerId(int $customerId): array {
        $sql = "SELECT a.*, v.license_plate, rt.description AS handeling
                FROM appointments a
                JOIN vehicles v ON a.vehicle_id = v.id
                LEFT JOIN repairs r ON r.appointment_id = a.id
                LEFT JOIN repair_types rt ON r.repair_type_id = rt.id
                WHERE a.customer_id = :customer_id
                ORDER BY a.appointment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Haal alle data van afspraken (voor flatpickr, etc.)
      public function getAllAppointmentDates(): array {
          // Haal data op waar 4 afspraken staan, blokkeer deze dagen
          $sql = "SELECT appointment_date, COUNT(*) as count FROM appointments GROUP BY appointment_date HAVING count >= 4";
          $stmt = $this->db->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll();

          $blockedDates = [];
          foreach ($result as $row) {
              $blockedDates[] = $row['appointment_date'];  // Moet in 'Y-m-d' formaat zijn
          }
          return $blockedDates;
      }


    // Haal afspraken op datum
    public function getByDate(string $date): array {
        $stmt = $this->db->prepare("
            SELECT a.*, v.license_plate, rt.description AS handeling, u.name AS mechanic_name
            FROM appointments a
            JOIN vehicles v ON a.vehicle_id = v.id
            LEFT JOIN repairs r ON r.appointment_id = a.id
            LEFT JOIN repair_types rt ON r.repair_type_id = rt.id
            LEFT JOIN users u ON a.mechanic_id = u.id
            WHERE a.appointment_date = :date
            ORDER BY a.appointment_date ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update status van een afspraak
    public function updateStatus(int $appointmentId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $appointmentId]);
    }

    // Wijs een monteur toe aan een afspraak
    public function assignMechanic(int $appointmentId, ?int $mechanicId): bool {
        $stmt = $this->db->prepare("UPDATE appointments SET mechanic_id = :mechanic_id WHERE id = :id");
        return $stmt->execute(['mechanic_id' => $mechanicId, 'id' => $appointmentId]);
    }

    // Update opmerkingen
    public function updateNotes(int $appointmentId, string $notes): bool {
        $sql = "UPDATE appointments SET notes = :notes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
