<?php
namespace App\Models;

use PDO;

class Vehicle {
    private $db;

    public function __construct($db) {
        $this->db = $db->getConnection();
    }

    public function addVehicle($customerId, $licensePlate, $brand = null, $model = null, $year = null) {
        // Check of kenteken al bestaat
        $sqlCheck = "SELECT id FROM vehicles WHERE license_plate = :license_plate";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bindParam(':license_plate', $licensePlate, PDO::PARAM_STR);
        $stmtCheck->execute();
        if ($stmtCheck->fetch()) {
            // Kenteken bestaat al, kan niet nogmaals
            return false;
        }

        $sql = "INSERT INTO vehicles (customer_id, license_plate, brand, model, year) 
                VALUES (:customer_id, :license_plate, :brand, :model, :year)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':license_plate', $licensePlate, PDO::PARAM_STR);
        $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
        $stmt->bindParam(':model', $model, PDO::PARAM_STR);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getVehiclesByCustomerId($customerId) {
        $sql = "SELECT * FROM vehicles WHERE customer_id = :customer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByLicensePlate(string $licensePlate) {
        $sql = "SELECT * FROM vehicles WHERE license_plate = :license_plate";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':license_plate', $licensePlate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
