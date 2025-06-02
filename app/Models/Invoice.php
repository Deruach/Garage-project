<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Invoice {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }
public function createFromAppointment(int $appointmentId, float $totaal): int {
    $sql = "INSERT INTO invoices (appointment_id, total_amount, paid, issue_date)
            VALUES (:appointment_id, :total_amount, 0, CURDATE())";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':appointment_id' => $appointmentId,
        ':total_amount' => $totaal
    ]);
    return (int) $this->db->lastInsertId();
}
public function getDetailsByAppointmentId(int $appointmentId): array {
    $sql = "
        SELECT 
            rt.description AS repair_description,
            rt.fixed_price,
            p.name AS part_name,
            p.price AS part_price,
            rp.quantity
        FROM appointments a
        JOIN repairs r ON r.appointment_id = a.id
        JOIN repair_types rt ON rt.id = r.repair_type_id
        LEFT JOIN repair_parts rp ON rp.repair_id = r.id
        LEFT JOIN parts p ON p.id = rp.part_id
        WHERE a.id = :appointment_id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':appointment_id' => $appointmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function berekenTotaalBedragVoorAppointment(int $appointmentId): float {
    $sql = "
        SELECT 
            rt.fixed_price,
            COALESCE(SUM(p.price * rp.quantity), 0) AS onderdelen_totaal
        FROM repairs r
        JOIN repair_types rt ON rt.id = r.repair_type_id
        LEFT JOIN repair_parts rp ON rp.repair_id = r.id
        LEFT JOIN parts p ON p.id = rp.part_id
        WHERE r.appointment_id = :appointment_id
        GROUP BY rt.fixed_price
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':appointment_id' => $appointmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) return 0;

    return (float) $result['fixed_price'] + (float) $result['onderdelen_totaal'];
}

    public function getByAppointmentId(int $appointmentId): ?array {
        $sql = "SELECT * FROM invoices WHERE appointment_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $appointmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function getById(int $invoiceId): ?array {
    $sql = "SELECT * FROM invoices WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $invoiceId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
public function createIfNotExists(int $appointmentId, float $amount): int {
    // Check of factuur al bestaat
    $existing = $this->getByAppointmentId($appointmentId);
    if ($existing) {
        return $existing['id'];
    }

    // Maak nieuwe factuur aan
    $sql = "INSERT INTO invoices (appointment_id, total_amount, paid, issue_date)
            VALUES (:appointment_id, :amount, 0, CURDATE())";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':appointment_id' => $appointmentId,
        ':amount' => $amount
    ]);
    return (int) $this->db->lastInsertId();
}


public function markAsPaid(int $invoiceId): bool {
    $sql = "UPDATE invoices SET paid = 1 WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $invoiceId]);
}

}
