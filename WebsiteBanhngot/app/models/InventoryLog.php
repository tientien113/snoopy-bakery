<?php
/**
 * Inventory Log Model
 */

class InventoryLog extends Model {
    protected $table = 'inventory_logs';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getProductLogs($productId, $limit = null) {
        $sql = "
            SELECT il.*, u.name as created_by_name 
            FROM {$this->table} il 
            LEFT JOIN users u ON il.created_by = u.id 
            WHERE il.product_id = :product_id 
            ORDER BY il.created_at DESC
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

    public function getRecentLogs($limit = 20) {
        $this->db->query("
            SELECT il.*, p.name as product_name, u.name as created_by_name 
            FROM {$this->table} il 
            LEFT JOIN products p ON il.product_id = p.id 
            LEFT JOIN users u ON il.created_by = u.id 
            ORDER BY il.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }
}
?>