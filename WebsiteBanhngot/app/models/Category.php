<?php
/**
 * Category Model
 */

class Category extends Model {
    protected $table = 'categories';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getWithProductCount() {
        $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM {$this->table} c 
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
            GROUP BY c.id 
            ORDER BY c.name
        ");
        return $this->db->results();
    }

    public function getActiveCategories() {
        $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM {$this->table} c 
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
            GROUP BY c.id 
            HAVING product_count > 0 
            ORDER BY c.name
        ");
        return $this->db->results();
    }
}
?>