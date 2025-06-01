<?php
namespace app\Controllers;

use app\Models\Review;

class ReviewController {
    private $reviewModel;

    public function __construct(Review $reviewModel) {
        $this->reviewModel = $reviewModel;
    }

    public function submitReview($klantId, $score, $tekst) {
        return $this->reviewModel->addReview($klantId, $score, $tekst);
    }

    public function listReviews() {
        return $this->reviewModel->getAllReviews();
    }
    public function listLatestReviews($limit = 4) {
        return $this->reviewModel->getLatestReviews($limit);
    }
}
?>