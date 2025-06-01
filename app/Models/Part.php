<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Part {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM parts ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT * FROM parts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
