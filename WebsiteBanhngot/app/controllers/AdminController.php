<?php
/**
 * Admin Controller - Trang quản trị
 */

class AdminController extends Controller {
    private $userModel;
    private $productModel;
    private $orderModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->categoryModel = new Category();
        $this->requireStaff(); // Only staff and above can access
    }

    public function dashboard() {
        $stats = $this->getDashboardStats();
        $recentOrders = $this->getRecentOrders();
        $lowStockProducts = $this->getLowStockProducts();

        $data = [
            'title' => 'Bảng điều khiển - ' . APP_NAME,
            'stats' => $stats,
            'recent_orders' => $recentOrders,
            'low_stock_products' => $lowStockProducts
        ];

        $this->view('admin/dashboard', $data);
    }

    public function products() {
        $page = $_GET['page'] ?? 1;
        $category_id = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';

        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";

        $countSql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";

        $params = [];

        if ($category_id) {
            $sql .= " AND p.category_id = :category_id";
            $countSql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        if ($search) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $countSql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        // Get total count
        $this->db->query($countSql);
        foreach ($params as $key => $value) {
            if ($key !== ':search') {
                $this->db->bind($key, $value);
            }
        }
        $totalResult = $this->db->result();
        $totalProducts = $totalResult['total'];
        $totalPages = ceil($totalProducts / $limit);

        // Get products
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $products = $this->db->results();

        $categories = $this->categoryModel->all();

        $data = [
            'title' => 'Quản lý Sản phẩm - ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'current_category' => $category_id,
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_products' => $totalProducts,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/products/index', $data);
    }

    public function createProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateProduct();
        }

        $categories = $this->categoryModel->all();

        $data = [
            'title' => 'Thêm Sản phẩm - ' . APP_NAME,
            'categories' => $categories,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/products/create', $data);
    }

    private function handleCreateProduct() {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $price = $_POST['price'] ?? 0;
        $discount_price = $_POST['discount_price'] ?? null;
        $quantity = $_POST['quantity'] ?? 0;
        $weight = $_POST['weight'] ?? null;
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $sku = trim($_POST['sku'] ?? '');

        // Validation
        $errors = [];

        if (empty($name)) $errors[] = 'Vui lòng nhập tên sản phẩm';
        if (empty($category_id)) $errors[] = 'Vui lòng chọn danh mục';
        if ($price <= 0) $errors[] = 'Giá sản phẩm phải lớn hơn 0';
        if ($quantity < 0) $errors[] = 'Số lượng không hợp lệ';

        if ($discount_price && $discount_price >= $price) {
            $errors[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->setFlash($error, FLASH_ERROR);
            }
            return;
        }

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadProductImage($_FILES['image']);
            if (!$imagePath) {
                $this->setFlash('Lỗi khi tải ảnh lên', FLASH_ERROR);
                return;
            }
        }

        $productData = [
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'ingredients' => $ingredients,
            'price' => $price,
            'discount_price' => $discount_price ?: null,
            'quantity' => $quantity,
            'weight' => $weight ?: null,
            'expiry_date' => $expiry_date ?: null,
            'sku' => $sku ?: null,
            'image' => $imagePath,
            'is_active' => PRODUCT_ACTIVE
        ];

        if ($this->productModel->create($productData)) {
            // Log inventory change
            $this->logInventoryChange(
                $this->db->lastInsertId(),
                INVENTORY_IMPORT,
                $quantity,
                0,
                $quantity,
                null,
                'import'
            );

            $this->setFlash('Thêm sản phẩm thành công!', FLASH_SUCCESS);
            $this->redirect('admin/products');
        } else {
            $this->setFlash('Có lỗi xảy ra khi thêm sản phẩm', FLASH_ERROR);
        }
    }

    public function editProduct($id) {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('Sản phẩm không tồn tại', FLASH_ERROR);
            $this->redirect('admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditProduct($id, $product);
            $product = $this->productModel->find($id); // Refresh data
        }

        $categories = $this->categoryModel->all();

        $data = [
            'title' => 'Sửa Sản phẩm - ' . APP_NAME,
            'product' => $product,
            'categories' => $categories,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/products/edit', $data);
    }

    private function handleEditProduct($id, $oldProduct) {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $price = $_POST['price'] ?? 0;
        $discount_price = $_POST['discount_price'] ?? null;
        $quantity = $_POST['quantity'] ?? 0;
        $weight = $_POST['weight'] ?? null;
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Validation
        $errors = [];

        if (empty($name)) $errors[] = 'Vui lòng nhập tên sản phẩm';
        if (empty($category_id)) $errors[] = 'Vui lòng chọn danh mục';
        if ($price <= 0) $errors[] = 'Giá sản phẩm phải lớn hơn 0';
        if ($quantity < 0) $errors[] = 'Số lượng không hợp lệ';

        if ($discount_price && $discount_price >= $price) {
            $errors[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->setFlash($error, FLASH_ERROR);
            }
            return;
        }

        // Handle image upload
        $imagePath = $oldProduct['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImagePath = $this->uploadProductImage($_FILES['image']);
            if ($newImagePath) {
                // Delete old image
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $imagePath = $newImagePath;
            }
        }

        $productData = [
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'ingredients' => $ingredients,
            'price' => $price,
            'discount_price' => $discount_price ?: null,
            'quantity' => $quantity,
            'weight' => $weight ?: null,
            'expiry_date' => $expiry_date ?: null,
            'sku' => $sku ?: null,
            'image' => $imagePath,
            'is_active' => $is_active,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Log inventory change if quantity changed
        $quantityChange = $quantity - $oldProduct['quantity'];
        if ($quantityChange != 0) {
            $this->logInventoryChange(
                $id,
                INVENTORY_ADJUST,
                $quantityChange,
                $oldProduct['quantity'],
                $quantity,
                null,
                'manual_adjust'
            );
        }

        if ($this->productModel->update($id, $productData)) {
            $this->setFlash('Cập nhật sản phẩm thành công!', FLASH_SUCCESS);
        } else {
            $this->setFlash('Có lỗi xảy ra khi cập nhật sản phẩm', FLASH_ERROR);
        }
    }

    public function deleteProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $product = $this->productModel->find($id);
        if (!$product) {
            $this->setFlash('Sản phẩm không tồn tại', FLASH_ERROR);
            $this->redirect('admin/products');
        }

        // Check if product has orders
        $this->db->query("SELECT COUNT(*) as count FROM order_items WHERE product_id = :product_id");
        $this->db->bind(':product_id', $id);
        $result = $this->db->result();

        if ($result['count'] > 0) {
            // Soft delete
            if ($this->productModel->update($id, ['is_active' => PRODUCT_INACTIVE])) {
                $this->setFlash('Đã ẩn sản phẩm thành công', FLASH_SUCCESS);
            } else {
                $this->setFlash('Có lỗi xảy ra khi ẩn sản phẩm', FLASH_ERROR);
            }
        } else {
            // Hard delete
            if ($this->productModel->hardDelete($id)) {
                // Delete product image
                if ($product['image'] && file_exists($product['image'])) {
                    unlink($product['image']);
                }
                $this->setFlash('Xóa sản phẩm thành công!', FLASH_SUCCESS);
            } else {
                $this->setFlash('Có lỗi xảy ra khi xóa sản phẩm', FLASH_ERROR);
            }
        }

        $this->redirect('admin/products');
    }

    public function orders() {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE 1=1";

        $countSql = "SELECT COUNT(*) as total FROM orders o WHERE 1=1";

        $params = [];

        if ($status) {
            $sql .= " AND o.status = :status";
            $countSql .= " AND o.status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (o.order_code LIKE :search OR o.customer_name LIKE :search OR o.customer_phone LIKE :search)";
            $countSql .= " AND (o.order_code LIKE :search OR o.customer_name LIKE :search OR o.customer_phone LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";

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
            'title' => 'Quản lý Đơn hàng - ' . APP_NAME,
            'orders' => $orders,
            'current_status' => $status,
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_orders' => $totalOrders,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/orders/index', $data);
    }

    public function orderDetail($id) {
        $order = $this->orderModel->find($id);
        
        if (!$order) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('admin/orders');
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

        // Get status history
        $this->db->query("
            SELECT osh.*, u.name as changed_by_name 
            FROM order_status_history osh 
            LEFT JOIN users u ON osh.changed_by_user_id = u.id 
            WHERE osh.order_id = :order_id 
            ORDER BY osh.created_at DESC
        ");
        $this->db->bind(':order_id', $id);
        $statusHistory = $this->db->results();

        $data = [
            'title' => 'Chi tiết Đơn hàng - ' . APP_NAME,
            'order' => $order,
            'order_items' => $orderItems,
            'payment' => $payment,
            'status_history' => $statusHistory,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/orders/detail', $data);
    }

    public function updateOrderStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $order = $this->orderModel->find($id);
        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        }

        $newStatus = $_POST['status'] ?? '';
        $note = trim($_POST['note'] ?? '');

        $validStatuses = [ORDER_PENDING, ORDER_CONFIRMED, ORDER_READY, ORDER_COMPLETED, ORDER_CANCELLED];
        if (!in_array($newStatus, $validStatuses)) {
            $this->jsonResponse(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
        }

        try {
            $this->db->beginTransaction();

            // Update order status
            if (!$this->orderModel->update($id, [
                'status' => $newStatus,
                'staff_id' => $_SESSION[SESSION_USER_ID],
                'updated_at' => date('Y-m-d H:i:s')
            ])) {
                throw new Exception('Failed to update order status');
            }

            // Log status change
            $this->logOrderStatusChange($id, $order['status'], $newStatus, $note);

            // If order is completed, update actual pickup time
            if ($newStatus === ORDER_COMPLETED && empty($order['actual_pickup_time'])) {
                $this->orderModel->update($id, ['actual_pickup_time' => date('Y-m-d H:i:s')]);
            }

            $this->db->commit();

            $this->setFlash('Cập nhật trạng thái đơn hàng thành công!', FLASH_SUCCESS);
            $this->jsonResponse(['success' => true, 'redirect' => BASE_URL . '/admin/orders/detail/' . $id]);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái']);
        }
    }

    public function users() {
        $this->requireAdmin(); // Only admin can manage users

        $page = $_GET['page'] ?? 1;
        $role_id = $_GET['role'] ?? '';
        $search = $_GET['search'] ?? '';

        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE 1=1";

        $countSql = "SELECT COUNT(*) as total FROM users u WHERE 1=1";

        $params = [];

        if ($role_id) {
            $sql .= " AND u.role_id = :role_id";
            $countSql .= " AND u.role_id = :role_id";
            $params[':role_id'] = $role_id;
        }

        if ($search) {
            $sql .= " AND (u.name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $countSql .= " AND (u.name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

        // Get total count
        $this->db->query($countSql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $totalResult = $this->db->result();
        $totalUsers = $totalResult['total'];
        $totalPages = ceil($totalUsers / $limit);

        // Get users
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $users = $this->db->results();

        $roles = (new Role())->all();

        $data = [
            'title' => 'Quản lý Người dùng - ' . APP_NAME,
            'users' => $users,
            'roles' => $roles,
            'current_role' => $role_id,
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_users' => $totalUsers,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/users/index', $data);
    }

    public function updateUserRole($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'Người dùng không tồn tại']);
        }

        $newRole = $_POST['role_id'] ?? '';
        $validRoles = [ROLE_CUSTOMER, ROLE_STAFF, ROLE_MANAGER, ROLE_ADMIN];

        if (!in_array($newRole, $validRoles)) {
            $this->jsonResponse(['success' => false, 'message' => 'Vai trò không hợp lệ']);
        }

        // Prevent changing own role
        if ($id == $_SESSION[SESSION_USER_ID]) {
            $this->jsonResponse(['success' => false, 'message' => 'Không thể thay đổi vai trò của chính mình']);
        }

        if ($this->userModel->update($id, ['role_id' => $newRole])) {
            $this->setFlash('Cập nhật vai trò thành công!', FLASH_SUCCESS);
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật vai trò']);
        }
    }

    public function toggleUserStatus($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $this->validateCSRF();

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'Người dùng không tồn tại']);
        }

        // Prevent deactivating own account
        if ($id == $_SESSION[SESSION_USER_ID]) {
            $this->jsonResponse(['success' => false, 'message' => 'Không thể vô hiệu hóa tài khoản của chính mình']);
        }

        $newStatus = $user['is_active'] ? USER_INACTIVE : USER_ACTIVE;

        if ($this->userModel->update($id, ['is_active' => $newStatus])) {
            $statusText = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';
            $this->setFlash("Đã {$statusText} tài khoản thành công!", FLASH_SUCCESS);
            $this->jsonResponse(['success' => true, 'new_status' => $newStatus]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi thay đổi trạng thái']);
        }
    }

    public function reports() {
        $period = $_GET['period'] ?? 'today'; // today, week, month, year
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        // Set date range based on period
        switch ($period) {
            case 'today':
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d');
                break;
            case 'week':
                $start_date = date('Y-m-d', strtotime('-1 week'));
                $end_date = date('Y-m-d');
                break;
            case 'month':
                $start_date = date('Y-m-d', strtotime('-1 month'));
                $end_date = date('Y-m-d');
                break;
            case 'year':
                $start_date = date('Y-m-d', strtotime('-1 year'));
                $end_date = date('Y-m-d');
                break;
        }

        $reports = $this->getSalesReport($start_date, $end_date);
        $topProducts = $this->getTopProducts($start_date, $end_date);

        $data = [
            'title' => 'Báo cáo & Thống kê - ' . APP_NAME,
            'reports' => $reports,
            'top_products' => $topProducts,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $this->view('admin/reports/index', $data);
    }

    private function getDashboardStats() {
        $stats = [];

        // Total orders
        $this->db->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $this->db->result()['total'];

        // Total customers
        $this->db->query("SELECT COUNT(*) as total FROM users WHERE role_id = " . ROLE_CUSTOMER);
        $stats['total_customers'] = $this->db->result()['total'];

        // Total products
        $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $stats['total_products'] = $this->db->result()['total'];

        // Today's revenue
        $this->db->query("SELECT SUM(final_price) as revenue FROM orders WHERE DATE(created_at) = CURDATE() AND status = '" . ORDER_COMPLETED . "'");
        $stats['today_revenue'] = $this->db->result()['revenue'] ?? 0;

        // Pending orders
        $this->db->query("SELECT COUNT(*) as total FROM orders WHERE status = '" . ORDER_PENDING . "'");
        $stats['pending_orders'] = $this->db->result()['total'];

        return $stats;
    }

    private function getRecentOrders($limit = 5) {
        $this->db->query("
            SELECT o.*, u.name as customer_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }

    private function getLowStockProducts($threshold = 10) {
        $this->db->query("
            SELECT * FROM products 
            WHERE quantity <= :threshold AND is_active = 1 
            ORDER BY quantity ASC 
            LIMIT 10
        ");
        $this->db->bind(':threshold', $threshold, PDO::PARAM_INT);
        return $this->db->results();
    }

    private function getSalesReport($start_date, $end_date) {
        $reports = [];

        // Total revenue
        $this->db->query("
            SELECT SUM(final_price) as revenue, COUNT(*) as orders 
            FROM orders 
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date 
            AND status = :completed_status
        ");
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $reports['summary'] = $this->db->result();

        // Revenue by day
        $this->db->query("
            SELECT DATE(created_at) as date, SUM(final_price) as revenue, COUNT(*) as orders 
            FROM orders 
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date 
            AND status = :completed_status
            GROUP BY DATE(created_at) 
            ORDER BY date
        ");
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $reports['daily'] = $this->db->results();

        return $reports;
    }

    private function getTopProducts($start_date, $end_date, $limit = 10) {
        $this->db->query("
            SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.unit_price) as total_revenue 
            FROM order_items oi 
            LEFT JOIN orders o ON oi.order_id = o.id 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date 
            AND o.status = :completed_status
            GROUP BY p.id, p.name 
            ORDER BY total_sold DESC 
            LIMIT :limit
        ");
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $this->db->bind(':completed_status', ORDER_COMPLETED);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->results();
    }

    private function uploadProductImage($file) {
        $uploadDir = AppConfig::getUploadPath('products');
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, ALLOWED_IMAGE_TYPES)) {
            return false;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return false;
        }

        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return 'uploads/products/' . $fileName;
        }

        return false;
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
            'created_by' => $_SESSION[SESSION_USER_ID]
        ];
        (new InventoryLog())->create($logData);
    }

    private function logOrderStatusChange($orderId, $oldStatus, $newStatus, $note = '') {
        $historyData = [
            'order_id' => $orderId,
            'changed_by_user_id' => $_SESSION[SESSION_USER_ID],
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note
        ];
        (new OrderStatusHistory())->create($historyData);
    }
}
?>