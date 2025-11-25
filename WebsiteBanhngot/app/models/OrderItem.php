<?php
/**
 * Order Item Model
 */

class OrderItem extends Model {
    protected $table = 'order_items';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getOrderItems($orderId) {
        $this->db->query("
            SELECT oi.*, p.name, p.image 
            FROM {$this->table} oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :order_id
        ");
        $this->db->bind(':order_id', $orderId);
        return $this->db->results();
    }

    public function getTopSellingProducts($limit = 10, $startDate = null, $endDate = null) {
        $sql = "
            SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.unit_price) as total_revenue 
            FROM {$this->table} oi 
            LEFT JOIN orders o ON oi.order_id = o.id 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE o.status = :completed_status
        ";

        if ($startDate && $endDate) {
            $sql .= " AND DATE(o.created_at) BETWEEN :start_date AND :end_date";
        }

        $sql .= " GROUP BY p.id, p.name ORDER BY total_sold DESC LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);

        if ($startDate && $endDate) {
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
        }

        return $this->db->results();
    }
}
?>