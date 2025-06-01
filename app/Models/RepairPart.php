<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class RepairPart {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function addPart(int $repairId, int $partId): bool {
        $sql = "INSERT INTO repair_parts (repair_id, part_id, quantity) VALUES (:repair_id, :part_id, 1)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':repair_id' => $repairId,
            ':part_id' => $partId
        ]);
    }
}
