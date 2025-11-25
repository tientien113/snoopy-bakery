<?php
/**
 * User Controller - Quản lý người dùng
 */

class UserController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function profile() {
        $this->requireLogin();

        $userId = $_SESSION[SESSION_USER_ID];
        $user = $this->userModel->find($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile($userId, $user);
            $user = $this->userModel->find($userId); // Refresh data
        }

        $data = [
            'title' => 'Thông tin cá nhân - ' . APP_NAME,
            'user' => $user,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('user/profile', $data);
    }

    private function updateProfile($userId, $currentUser) {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $current_password = trim($_POST['current_password'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        $errors = [];

        // Basic validation
        if (empty($name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        // Check if changing password
        if (!empty($current_password)) {
            if (!password_verify($current_password, $currentUser['password'])) {
                $errors[] = 'Mật khẩu hiện tại không đúng';
            }

            if (empty($new_password)) {
                $errors[] = 'Vui lòng nhập mật khẩu mới';
            } elseif (strlen($new_password) < MIN_PASSWORD_LENGTH) {
                $errors[] = 'Mật khẩu mới phải có ít nhất ' . MIN_PASSWORD_LENGTH . ' ký tự';
            } elseif ($new_password !== $confirm_password) {
                $errors[] = 'Mật khẩu xác nhận không khớp';
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->setFlash($error, FLASH_ERROR);
            }
            return;
        }

        // Prepare update data
        $updateData = [
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add password if changing
        if (!empty($new_password)) {
            $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT, ['cost' => PASSWORD_COST]);
        }

        if ($this->userModel->update($userId, $updateData)) {
            // Update session name if changed
            $_SESSION[SESSION_USER_NAME] = $name;
            
            $this->setFlash('Cập nhật thông tin thành công!', FLASH_SUCCESS);
        } else {
            $this->setFlash('Có lỗi xảy ra khi cập nhật thông tin', FLASH_ERROR);
        }
    }

    public function orders() {
        $this->requireLogin();

        $userId = $_SESSION[SESSION_USER_ID];
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';

        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT * FROM orders WHERE user_id = :user_id";
        $countSql = "SELECT COUNT(*) as total FROM orders WHERE user_id = :user_id";

        $params = [':user_id' => $userId];

        if ($status) {
            $sql .= " AND status = :status";
            $countSql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        // Get total count
        $this->db->query($countSql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $totalResult = $this->db->result();
        $totalOrders = $totalResult['total'];
        $totalPages = ceil($totalOrders / $limit);

        // Get orders
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $orders = $this->db->results();

        $data = [
            'title' => 'Đơn hàng của tôi - ' . APP_NAME,
            'orders' => $orders,
            'current_status' => $status,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_orders' => $totalOrders
        ];

        $this->view('user/orders', $data);
    }

    public function reviews() {
        $this->requireLogin();

        $userId = $_SESSION[SESSION_USER_ID];
        
        $this->db->query("
            SELECT r.*, p.name as product_name, p.image as product_image 
            FROM reviews r 
            LEFT JOIN products p ON r.product_id = p.id 
            WHERE r.user_id = :user_id 
            ORDER BY r.created_at DESC
        ");
        $this->db->bind(':user_id', $userId);
        $reviews = $this->db->results();

        $data = [
            'title' => 'Đánh giá của tôi - ' . APP_NAME,
            'reviews' => $reviews
        ];

        $this->view('user/reviews', $data);
    }

    public function addReview() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $productId = $_POST['product_id'] ?? null;
        $orderId = $_POST['order_id'] ?? null;
        $rating = $_POST['rating'] ?? 0;
        $comment = trim($_POST['comment'] ?? '');

        if (!$productId || !$orderId) {
            $this->jsonResponse(['success' => false, 'message' => 'Thiếu thông tin sản phẩm hoặc đơn hàng']);
        }

        // Verify that user has purchased this product
        $this->db->query("
            SELECT oi.id 
            FROM order_items oi 
            LEFT JOIN orders o ON oi.order_id = o.id 
            WHERE oi.order_id = :order_id 
            AND oi.product_id = :product_id 
            AND o.user_id = :user_id 
            AND o.status = :completed_status
        ");
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':user_id', $_SESSION[SESSION_USER_ID]);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $purchase = $this->db->result();

        if (!$purchase) {
            $this->jsonResponse(['success' => false, 'message' => 'Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn thành']);
        }

        // Check if already reviewed
        $this->db->query("
            SELECT id FROM reviews 
            WHERE user_id = :user_id AND product_id = :product_id AND order_id = :order_id
        ");
        $this->db->bind(':user_id', $_SESSION[SESSION_USER_ID]);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':order_id', $orderId);
        $existingReview = $this->db->result();

        if ($existingReview) {
            $this->jsonResponse(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
        }

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            $this->jsonResponse(['success' => false, 'message' => 'Đánh giá phải từ 1 đến 5 sao']);
        }

        $reviewData = [
            'user_id' => $_SESSION[SESSION_USER_ID],
            'product_id' => $productId,
            'order_id' => $orderId,
            'rating' => $rating,
            'comment' => $comment
        ];

        if ((new Review())->create($reviewData)) {
            $this->jsonResponse(['success' => true, 'message' => 'Đã thêm đánh giá thành công']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm đánh giá']);
        }
    }
}
?>