<?php
class CartHelper {
    
    // Get cart count
    public static function getCartCount() {
        if (!AuthHelper::isLoggedIn()) {
            return 0;
        }
        
        $db = new Database();
        $db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $db->bind(':user_id', AuthHelper::getUserId());
        $result = $db->single();
        
        return $result['total'] ?? 0;
    }
    
    // Get cart total price
    public static function getCartTotal() {
        if (!AuthHelper::isLoggedIn()) {
            return 0;
        }
        
        $db = new Database();
        $db->query("SELECT SUM(c.quantity * COALESCE(p.discount_price, p.price)) as total 
                   FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.user_id = :user_id AND p.is_active = 1");
        $db->bind(':user_id', AuthHelper::getUserId());
        $result = $db->single();
        
        return $result['total'] ?? 0;
    }
    
    // Get cart items with product details
    public static function getCartItems() {
        if (!AuthHelper::isLoggedIn()) {
            return [];
        }
        
        $db = new Database();
        $db->query("SELECT c.*, p.name, p.price, p.discount_price, p.image, p.quantity as stock_quantity,
                   COALESCE(p.discount_price, p.price) as final_price,
                   (c.quantity * COALESCE(p.discount_price, p.price)) as item_total
                   FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.user_id = :user_id AND p.is_active = 1
                   ORDER BY c.created_at DESC");
        $db->bind(':user_id', AuthHelper::getUserId());
        
        return $db->resultSet();
    }
    
    // Add item to cart
    public static function addToCart($productId, $quantity = 1) {
        if (!AuthHelper::isLoggedIn()) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }
        
        // Check if product exists and is active
        $db = new Database();
        $db->query("SELECT id, quantity FROM products WHERE id = :id AND is_active = 1");
        $db->bind(':id', $productId);
        $product = $db->single();
        
        if (!$product) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }
        
        // Check stock
        if ($product['quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Số lượng sản phẩm không đủ'];
        }
        
        // Check if item already in cart
        $db->query("SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $db->bind(':user_id', AuthHelper::getUserId());
        $db->bind(':product_id', $productId);
        $existingItem = $db->single();
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            if ($newQuantity > $product['quantity']) {
                return ['success' => false, 'message' => 'Số lượng vượt quá tồn kho'];
            }
            
            $db->query("UPDATE cart SET quantity = :quantity, updated_at = NOW() WHERE id = :id");
            $db->bind(':quantity', $newQuantity);
            $db->bind(':id', $existingItem['id']);
        } else {
            // Add new item
            $db->query("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $db->bind(':user_id', AuthHelper::getUserId());
            $db->bind(':product_id', $productId);
            $db->bind(':quantity', $quantity);
        }
        
        if ($db->execute()) {
            return ['success' => true, 'message' => 'Đã thêm vào giỏ hàng'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi thêm vào giỏ hàng'];
        }
    }
    
    // Update cart item quantity
    public static function updateCartItem($cartId, $quantity) {
        if (!AuthHelper::isLoggedIn()) {
            return false;
        }
        
        // Check stock
        $db = new Database();
        $db->query("SELECT p.quantity as stock_quantity FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.id = :cart_id AND c.user_id = :user_id");
        $db->bind(':cart_id', $cartId);
        $db->bind(':user_id', AuthHelper::getUserId());
        $item = $db->single();
        
        if (!$item || $quantity > $item['stock_quantity']) {
            return false;
        }
        
        $db->query("UPDATE cart SET quantity = :quantity, updated_at = NOW() WHERE id = :id AND user_id = :user_id");
        $db->bind(':quantity', $quantity);
        $db->bind(':id', $cartId);
        $db->bind(':user_id', AuthHelper::getUserId());
        
        return $db->execute();
    }
    
    // Remove item from cart
    public static function removeFromCart($cartId) {
        if (!AuthHelper::isLoggedIn()) {
            return false;
        }
        
        $db = new Database();
        $db->query("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
        $db->bind(':id', $cartId);
        $db->bind(':user_id', AuthHelper::getUserId());
        
        return $db->execute();
    }
    
    // Clear cart
    public static function clearCart() {
        if (!AuthHelper::isLoggedIn()) {
            return false;
        }
        
        $db = new Database();
        $db->query("DELETE FROM cart WHERE user_id = :user_id");
        $db->bind(':user_id', AuthHelper::getUserId());
        
        return $db->execute();
    }
}
?>