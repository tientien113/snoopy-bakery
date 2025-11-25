<?php
/**
 * Cart Controller - Quản lý giỏ hàng
 */

class CartController extends Controller {
    private $cartModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->requireLogin();
    }

    public function index() {
        $userId = $_SESSION[SESSION_USER_ID];
        $cartItems = $this->getCartItems($userId);

        $data = [
            'title' => 'Giỏ hàng - ' . APP_NAME,
            'cart_items' => $cartItems,
            'total_price' => $this->calculateTotalPrice($cartItems),
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('cart/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$productId) {
            $this->jsonResponse(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
        }

        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }

        if ($product['quantity'] < $quantity) {
            $this->jsonResponse(['success' => false, 'message' => 'Số lượng sản phẩm không đủ']);
        }

        $userId = $_SESSION[SESSION_USER_ID];

        // Check if product already in cart
        $existingItem = $this->cartModel->where([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        if (!empty($existingItem)) {
            // Update quantity
            $newQuantity = $existingItem[0]['quantity'] + $quantity;
            if ($newQuantity > $product['quantity']) {
                $this->jsonResponse(['success' => false, 'message' => 'Số lượng sản phẩm không đủ']);
            }

            $this->cartModel->update($existingItem[0]['id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item
            $cartData = [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ];
            $this->cartModel->create($cartData);
        }

        // Update cart count in session
        $_SESSION[SESSION_CART_COUNT] = $this->getCartCount($userId);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $_SESSION[SESSION_CART_COUNT]
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $cartId = $_POST['cart_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$cartId) {
            $this->jsonResponse(['success' => false, 'message' => 'Thiếu thông tin giỏ hàng']);
        }

        $cartItem = $this->cartModel->find($cartId);
        if (!$cartItem || $cartItem['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->jsonResponse(['success' => false, 'message' => 'Mục giỏ hàng không tồn tại']);
        }

        $product = $this->productModel->find($cartItem['product_id']);
        if ($quantity > $product['quantity']) {
            $this->jsonResponse(['success' => false, 'message' => 'Số lượng sản phẩm không đủ']);
        }

        if ($quantity <= 0) {
            $this->cartModel->hardDelete($cartId);
        } else {
            $this->cartModel->update($cartId, ['quantity' => $quantity]);
        }

        // Update cart count in session
        $userId = $_SESSION[SESSION_USER_ID];
        $_SESSION[SESSION_CART_COUNT] = $this->getCartCount($userId);

        $cartItems = $this->getCartItems($userId);

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $_SESSION[SESSION_CART_COUNT],
            'total_price' => $this->calculateTotalPrice($cartItems)
        ]);
    }

    public function remove($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $cartItem = $this->cartModel->find($id);
        if (!$cartItem || $cartItem['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->setFlash('Mục giỏ hàng không tồn tại', FLASH_ERROR);
            $this->redirect('cart');
        }

        if ($this->cartModel->hardDelete($id)) {
            // Update cart count in session
            $userId = $_SESSION[SESSION_USER_ID];
            $_SESSION[SESSION_CART_COUNT] = $this->getCartCount($userId);
            
            $this->setFlash('Đã xóa sản phẩm khỏi giỏ hàng', FLASH_SUCCESS);
        } else {
            $this->setFlash('Có lỗi xảy ra khi xóa sản phẩm', FLASH_ERROR);
        }

        $this->redirect('cart');
    }

    private function getCartItems($userId) {
        $this->db->query("
            SELECT c.*, p.name, p.price, p.discount_price, p.image, p.quantity as stock_quantity 
            FROM cart c 
            LEFT JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id AND p.is_active = 1
        ");
        $this->db->bind(':user_id', $userId);
        return $this->db->results();
    }

    private function calculateTotalPrice($cartItems) {
        $total = 0;
        foreach ($cartItems as $item) {
            $price = $item['discount_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }

    private function getCartCount($userId) {
        $this->db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->result();
        return $result['total'] ?? 0;
    }
}
?>