<?php
class AuthHelper {
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Get current user ID
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    // Get current user role
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? ROLE_CUSTOMER;
    }
    
    // Check if user is admin
    public static function isAdmin() {
        return self::getUserRole() == ROLE_ADMIN;
    }
    
    // Check if user is manager
    public static function isManager() {
        return self::getUserRole() == ROLE_MANAGER;
    }
    
    // Check if user is staff
    public static function isStaff() {
        return self::getUserRole() == ROLE_STAFF;
    }
    
    // Check if user is customer
    public static function isCustomer() {
        return self::getUserRole() == ROLE_CUSTOMER;
    }
    
    // Check if user has permission (admin, manager, staff)
    public static function hasPermission() {
        $role = self::getUserRole();
        return in_array($role, [ROLE_ADMIN, ROLE_MANAGER, ROLE_STAFF]);
    }
    
    // Redirect to login if not authenticated
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }
    
    // Redirect to home if already logged in
    public static function requireGuest() {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/home');
            exit();
        }
    }
    
    // Set user session
    public static function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time();
    }
    
    // Clear user session
    public static function logout() {
        session_unset();
        session_destroy();
    }
    
    // Check session timeout
    public static function checkSessionTimeout() {
        if (isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
                self::logout();
                header('Location: ' . BASE_URL . '/login?timeout=1');
                exit();
            }
            // Update last activity time
            $_SESSION['login_time'] = time();
        }
    }
}
?>