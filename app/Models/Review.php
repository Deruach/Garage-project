<?php

namespace app\Models;

use PDO;

class Review {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function addReview($customerId, $rating, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (customer_id, rating, comment, review_date)
VALUES (:customer_id, :rating, :comment, NOW())

        ");
        return $stmt->execute([
            ':customer_id' => $customerId,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
    }

    public function getAllReviews() {
        $stmt = $this->db->query("
            SELECT r.*, u.name
            FROM reviews r
            JOIN users u ON r.customer_id = u.id
            ORDER BY review_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  public function getLatestReviews($limit = 4) {
      $stmt = $this->db->prepare("
          SELECT r.*, u.name 
          FROM reviews r 
          JOIN users u ON r.customer_id = u.id 
          ORDER BY r.review_date DESC 
          LIMIT :limit
      ");
      $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

}
