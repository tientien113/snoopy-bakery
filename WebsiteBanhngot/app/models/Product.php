<?php
/**
 * Product Model
 */

class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getFeatured($limit = 6) {
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            AND p.discount_price IS NOT NULL 
            ORDER BY p.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }

    public function getNewArrivals($limit = 8) {
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY p.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }

    public function getByCategory($categoryId, $limit = null) {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = :category_id 
            AND p.is_active = 1 
            ORDER BY p.created_at DESC
        ";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $this->db->query($sql);
        $this->db->bind(':category_id', $categoryId);
        
        if ($limit) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        }

        return $this->db->results();
    }

    public function search($keyword, $categoryId = null) {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            AND (p.name LIKE :keyword OR p.description LIKE :keyword OR c.name LIKE :keyword)
        ";

        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':keyword', "%$keyword%");

        if ($categoryId) {
            $this->db->bind(':category_id', $categoryId);
        }

        return $this->db->results();
    }

    public function updateStock($productId, $quantity) {
        $this->db->query("UPDATE {$this->table} SET quantity = quantity + :quantity WHERE id = :id");
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':id', $productId);
        return $this->db->execute();
    }

    public function getLowStock($threshold = 10) {
        $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE quantity <= :threshold AND is_active = 1 
            ORDER BY quantity ASC
        ");
        $this->db->bind(':threshold', $threshold, PDO::PARAM_INT);
        return $this->db->results();
    }
}
?>