<?php
/**
 * Auth Controller - Xử lý đăng nhập, đăng ký
 */

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function login() {
        // Nếu đã đăng nhập, chuyển hướng
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        }

        $data = [
            'title' => 'Đăng nhập - ' . APP_NAME,
            'csrf_token' => $this->generateCSRFToken()
        ];
        $this->view('auth/login', $data);
    }

    private function handleLogin() {
        $this->validateCSRF();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validation
        if (empty($email) || empty($password)) {
            $this->setFlash('Vui lòng điền đầy đủ email và mật khẩu', FLASH_ERROR);
            return;
        }

        // Find user
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$user['is_active']) {
            $this->setFlash('Email hoặc mật khẩu không đúng', FLASH_ERROR);
            return;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->setFlash('Email hoặc mật khẩu không đúng', FLASH_ERROR);
            return;
        }

        // Set session
        $_SESSION[SESSION_USER_ID] = $user['id'];
        $_SESSION[SESSION_USER_ROLE] = $user['role_id'];
        $_SESSION[SESSION_USER_NAME] = $user['name'];
        $_SESSION[SESSION_CART_COUNT] = $this->getCartCount($user['id']);

        $this->setFlash('Đăng nhập thành công!', FLASH_SUCCESS);
        
        // Redirect based on role
        if ($user['role_id'] == ROLE_CUSTOMER) {
            $this->redirect('home');
        } else {
            $this->redirect('admin');
        }
    }

    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        }

        $data = [
            'title' => 'Đăng ký - ' . APP_NAME,
            'csrf_token' => $this->generateCSRFToken()
        ];
        $this->view('auth/register', $data);
    }

    private function handleRegister() {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validation
        $errors = [];

        if (empty($name)) $errors[] = 'Vui lòng nhập họ tên';
        if (empty($email)) $errors[] = 'Vui lòng nhập email';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (empty($password)) $errors[] = 'Vui lòng nhập mật khẩu';
        if (strlen($password) < MIN_PASSWORD_LENGTH) $errors[] = 'Mật khẩu phải có ít nhất ' . MIN_PASSWORD_LENGTH . ' ký tự';
        if ($password !== $confirm_password) $errors[] = 'Mật khẩu xác nhận không khớp';

        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email đã được sử dụng';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->setFlash($error, FLASH_ERROR);
            }
            return;
        }

        // Create user
        $userData = [
            'role_id' => ROLE_CUSTOMER,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT, ['cost' => PASSWORD_COST]),
            'phone' => $phone,
            'address' => $address,
            'is_active' => USER_ACTIVE
        ];

        if ($this->userModel->create($userData)) {
            $this->setFlash('Đăng ký thành công! Vui lòng đăng nhập.', FLASH_SUCCESS);
            $this->redirect('auth/login');
        } else {
            $this->setFlash('Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.', FLASH_ERROR);
        }
    }

    public function logout() {
        session_destroy();
        $this->setFlash('Đã đăng xuất thành công', FLASH_SUCCESS);
        $this->redirect('auth/login');
    }

    private function getCartCount($userId) {
        $this->db->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->result();
        return $result['total'] ?? 0;
    }
}
?>