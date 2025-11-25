<?php
/**
 * Cart Model
 */

class Cart extends Model {
    protected $table = 'cart';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getUserCart($userId) {
        $this->db->query("
            SELECT c.*, p.name, p.price, p.discount_price, p.image, p.quantity as stock_quantity 
            FROM {$this->table} c 
            LEFT JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id AND p.is_active = 1
        ");
        $this->db->bind(':user_id', $userId);
        return $this->db->results();
    }

    public function getCartItem($userId, $productId) {
        $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->result();
    }

    public function updateQuantity($cartId, $quantity) {
        $this->db->query("UPDATE {$this->table} SET quantity = :quantity WHERE id = :id");
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':id', $cartId);
        return $this->db->execute();
    }

    public function clearUserCart($userId) {
        $this->db->query("DELETE FROM {$this->table} WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    public function getCartCount($userId) {
        $this->db->query("SELECT SUM(quantity) as total FROM {$this->table} WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->result();
        return $result['total'] ?? 0;
    }
}
?>