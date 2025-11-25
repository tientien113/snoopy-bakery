<?php
/**
 * Review Model
 */

class Review extends Model {
    protected $table = 'reviews';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getProductReviews($productId, $limit = null) {
        $sql = "
            SELECT r.*, u.name as user_name 
            FROM {$this->table} r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = :product_id 
            ORDER BY r.created_at DESC
        ";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);

        if ($limit) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        }

        return $this->db->results();
    }

    public function getUserReview($userId, $productId) {
        $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->result();
    }

    public function getProductRatingStats($productId) {
        $this->db->query("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
            FROM {$this->table} 
            WHERE product_id = :product_id
        ");
        $this->db->bind(':product_id', $productId);
        return $this->db->result();
    }

    public function getRecentReviews($limit = 10) {
        $this->db->query("
            SELECT r.*, u.name as user_name, p.name as product_name 
            FROM {$this->table} r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN products p ON r.product_id = p.id 
            ORDER BY r.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }
}
?>