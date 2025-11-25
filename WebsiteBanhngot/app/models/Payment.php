<?php
/**
 * Payment Model
 */

class Payment extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getOrderPayment($orderId) {
        $this->db->query("SELECT * FROM {$this->table} WHERE order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->result();
    }

    public function updateStatus($paymentId, $status, $providerReference = null) {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === PAYMENT_PAID) {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }

        if ($providerReference) {
            $data['provider_reference'] = $providerReference;
        }

        return $this->update($paymentId, $data);
    }

    public function getRevenueByMethod($startDate = null, $endDate = null) {
        $sql = "
            SELECT method, SUM(amount) as total_revenue, COUNT(*) as total_payments 
            FROM {$this->table} 
            WHERE status = :paid_status
        ";

        if ($startDate && $endDate) {
            $sql .= " AND DATE(created_at) BETWEEN :start_date AND :end_date";
        }

        $sql .= " GROUP BY method";

        $this->db->query($sql);
        $this->db->bind(':paid_status', PAYMENT_PAID);

        if ($startDate && $endDate) {
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
        }

        return $this->db->results();
    }
}
?>