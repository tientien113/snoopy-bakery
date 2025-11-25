<?php
/**
 * Order Model
 */

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        // Add created_at timestamp
        $data['created_at'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }

    public function getUserOrders($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        if ($limit) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }

        return $this->db->results();
    }

    public function getOrdersByStatus($status, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);
        $this->db->bind(':status', $status);
        
        if ($limit) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }

        return $this->db->results();
    }

    public function getOrderWithDetails($orderId) {
        // Get order basic info
        $order = $this->find($orderId);
        if (!$order) {
            return null;
        }

        // Get order items
        $this->db->query("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :order_id
        ");
        $this->db->bind(':order_id', $orderId);
        $order['items'] = $this->db->results();

        // Get customer info
        $this->db->query("SELECT name, email, phone FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $order['user_id']);
        $order['customer'] = $this->db->result();

        return $order;
    }

    public function getDailyRevenue($date) {
        $this->db->query("
            SELECT SUM(final_price) as revenue, COUNT(*) as orders 
            FROM {$this->table} 
            WHERE DATE(created_at) = :date 
            AND status = :completed_status
        ");
        $this->db->bind(':date', $date);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        return $this->db->result();
    }

    public function getRevenueByDateRange($startDate, $endDate) {
        $this->db->query("
            SELECT DATE(created_at) as date, SUM(final_price) as revenue, COUNT(*) as orders 
            FROM {$this->table} 
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date 
            AND status = :completed_status
            GROUP BY DATE(created_at) 
            ORDER BY date
        ");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        return $this->db->results();
    }

    public function getStats() {
        $stats = [];

        // Total orders
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $stats['total_orders'] = $this->db->result()['total'];

        // Total revenue
        $this->db->query("SELECT SUM(final_price) as revenue FROM {$this->table} WHERE status = :completed_status");
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $stats['total_revenue'] = $this->db->result()['revenue'] ?? 0;

        // Today's revenue
        $this->db->query("
            SELECT SUM(final_price) as revenue 
            FROM {$this->table} 
            WHERE DATE(created_at) = CURDATE() 
            AND status = :completed_status
        ");
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $stats['today_revenue'] = $this->db->result()['revenue'] ?? 0;

        // Orders by status
        $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM {$this->table} 
            GROUP BY status
        ");
        $stats['status_counts'] = $this->db->results();

        return $stats;
    }
}
?>