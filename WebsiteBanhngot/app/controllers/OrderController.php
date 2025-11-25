<?php
/**
 * Order Controller - Quản lý đơn hàng
 */

class OrderController extends Controller {
    private $orderModel;
    private $orderItemModel;
    private $cartModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    public function checkout() {
        $this->requireLogin();

        $userId = $_SESSION[SESSION_USER_ID];
        $cartItems = $this->getCartItems($userId);

        if (empty($cartItems)) {
            $this->setFlash('Giỏ hàng trống', FLASH_ERROR);
            $this->redirect('cart');
        }

        // Check stock
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock_quantity']) {
                $this->setFlash("Sản phẩm {$item['name']} chỉ còn {$item['stock_quantity']} sản phẩm", FLASH_ERROR);
                $this->redirect('cart');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCheckout($userId, $cartItems);
        }

        $user = (new User())->find($userId);

        $data = [
            'title' => 'Thanh toán - ' . APP_NAME,
            'cart_items' => $cartItems,
            'total_price' => $this->calculateTotalPrice($cartItems),
            'user' => $user,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('orders/checkout', $data);
    }

    private function handleCheckout($userId, $cartItems) {
        $this->validateCSRF();

        $customerName = trim($_POST['customer_name'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');
        $pickupTime = $_POST['pickup_time'] ?? '';
        $pickupNote = trim($_POST['pickup_note'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? PAYMENT_CASH_PICKUP;

        // Validation
        if (empty($customerName) || empty($customerPhone)) {
            $this->setFlash('Vui lòng điền đầy đủ thông tin liên hệ', FLASH_ERROR);
            return;
        }

        if (empty($pickupTime)) {
            $this->setFlash('Vui lòng chọn thời gian lấy hàng', FLASH_ERROR);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Calculate total
            $totalPrice = $this->calculateTotalPrice($cartItems);
            $orderCode = $this->generateOrderCode();

            // Create order
            $orderData = [
                'user_id' => $userId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'pickup_time' => $pickupTime,
                'pickup_note' => $pickupNote,
                'status' => ORDER_PENDING
            ];

            $orderId = $this->orderModel->create($orderData);

            if (!$orderId) {
                throw new Exception('Failed to create order');
            }

            // Create order items and update inventory
            foreach ($cartItems as $item) {
                $unitPrice = $item['discount_price'] ?? $item['price'];
                
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice
                ];

                if (!$this->orderItemModel->create($orderItemData)) {
                    throw new Exception('Failed to create order item');
                }

                // Update product quantity
                $newQuantity = $item['stock_quantity'] - $item['quantity'];
                if (!$this->productModel->update($item['product_id'], ['quantity' => $newQuantity])) {
                    throw new Exception('Failed to update product quantity');
                }

                // Log inventory change
                $this->logInventoryChange(
                    $item['product_id'],
                    INVENTORY_SOLD,
                    -$item['quantity'],
                    $item['stock_quantity'],
                    $newQuantity,
                    $orderId,
                    'order'
                );
            }

            // Create payment record
            $paymentData = [
                'order_id' => $orderId,
                'method' => $paymentMethod,
                'amount' => $totalPrice,
                'status' => ($paymentMethod == PAYMENT_CASH_PICKUP) ? PAYMENT_PENDING : PAYMENT_PENDING
            ];

            if (!(new Payment())->create($paymentData)) {
                throw new Exception('Failed to create payment record');
            }

            // Clear cart
            $this->cartModel->clearUserCart($userId);

            // Update session cart count
            $_SESSION[SESSION_CART_COUNT] = 0;

            $this->db->commit();

            $this->setFlash('Đặt hàng thành công! Mã đơn hàng: ' . $orderCode, FLASH_SUCCESS);
            $this->redirect('orders/success/' . $orderId);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->setFlash('Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.', FLASH_ERROR);
        }
    }

    public function success($orderId) {
        $this->requireLogin();

        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('home');
        }

        $data = [
            'title' => 'Đặt hàng thành công - ' . APP_NAME,
            'order' => $order
        ];

        $this->view('orders/success', $data);
    }

    public function history() {
        $this->requireLogin();

        $userId = $_SESSION[SESSION_USER_ID];
        $page = $_GET['page'] ?? 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $this->db->query("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $orders = $this->db->results();

        // Get total count
        $this->db->query("SELECT COUNT(*) as total FROM orders WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $totalResult = $this->db->result();
        $totalPages = ceil($totalResult['total'] / $limit);

        $data = [
            'title' => 'Lịch sử đơn hàng - ' . APP_NAME,
            'orders' => $orders,
            'current_page' => $page,
            'total_pages' => $totalPages
        ];

        $this->view('orders/history', $data);
    }

    public function detail($id) {
        $this->requireLogin();

        $order = $this->orderModel->find($id);
        if (!$order || ($order['user_id'] != $_SESSION[SESSION_USER_ID] && !$this->isStaff())) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('home');
        }

        // Get order items
        $this->db->query("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :order_id
        ");
        $this->db->bind(':order_id', $id);
        $orderItems = $this->db->results();

        // Get payment info
        $payment = (new Payment())->where(['order_id' => $id])[0] ?? null;

        $data = [
            'title' => 'Chi tiết đơn hàng ' . $order['order_code'] . ' - ' . APP_NAME,
            'order' => $order,
            'order_items' => $orderItems,
            'payment' => $payment
        ];

        $this->view('orders/detail', $data);
    }

    public function cancel($id) {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $order = $this->orderModel->find($id);
        if (!$order || $order['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->jsonResponse(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        }

        if ($order['status'] != ORDER_PENDING) {
            $this->jsonResponse(['success' => false, 'message' => 'Không thể hủy đơn hàng này']);
        }

        try {
            $this->db->beginTransaction();

            // Update order status
            if (!$this->orderModel->update($id, ['status' => ORDER_CANCELLED])) {
                throw new Exception('Failed to update order status');
            }

            // Restore product quantities
            $orderItems = $this->orderItemModel->where(['order_id' => $id]);
            foreach ($orderItems as $item) {
                $product = $this->productModel->find($item['product_id']);
                $newQuantity = $product['quantity'] + $item['quantity'];
                
                if (!$this->productModel->update($item['product_id'], ['quantity' => $newQuantity])) {
                    throw new Exception('Failed to restore product quantity');
                }

                // Log inventory change
                $this->logInventoryChange(
                    $item['product_id'],
                    INVENTORY_RETURN,
                    $item['quantity'],
                    $product['quantity'],
                    $newQuantity,
                    $id,
                    'order_cancel'
                );
            }

            // Update payment status if paid
            $payment = (new Payment())->where(['order_id' => $id])[0] ?? null;
            if ($payment && $payment['status'] == PAYMENT_PAID) {
                (new Payment())->update($payment['id'], ['status' => PAYMENT_REFUNDED]);
            }

            $this->db->commit();

            $this->setFlash('Đã hủy đơn hàng thành công', FLASH_SUCCESS);
            $this->jsonResponse(['success' => true, 'redirect' => BASE_URL . '/orders/history']);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi hủy đơn hàng']);
        }
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

    private function generateOrderCode() {
        $date = date('Ymd');
        $random = mt_rand(1000, 9999);
        return ORDER_CODE_PREFIX . $date . $random;
    }

    private function logInventoryChange($productId, $changeType, $changeQuantity, $oldQuantity, $newQuantity, $referenceId, $referenceType) {
        $logData = [
            'product_id' => $productId,
            'change_type' => $changeType,
            'change_quantity' => $changeQuantity,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'created_by' => $_SESSION[SESSION_USER_ID] ?? null
        ];
        (new InventoryLog())->create($logData);
    }
}
?>