<?php
/**
 * Order Status History Model
 */

class OrderStatusHistory extends Model {
    protected $table = 'order_status_history';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getOrderHistory($orderId) {
        $this->db->query("
            SELECT osh.*, u.name as changed_by_name 
            FROM {$this->table} osh 
            LEFT JOIN users u ON osh.changed_by_user_id = u.id 
            WHERE osh.order_id = :order_id 
            ORDER BY osh.created_at DESC
        ");
        $this->db->bind(':order_id', $orderId);
        return $this->db->results();
    }

    public function logStatusChange($orderId, $oldStatus, $newStatus, $changedByUserId = null, $note = '') {
        $data = [
            'order_id' => $orderId,
            'changed_by_user_id' => $changedByUserId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note
        ];

        return $this->create($data);
    }
}
?>