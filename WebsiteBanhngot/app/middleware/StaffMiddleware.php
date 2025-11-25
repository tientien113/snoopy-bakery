<?php
class StaffMiddleware {
    
    public function handle() {
        if (!AuthHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if (!AuthHelper::hasPermission()) {
            header('Location: ' . BASE_URL . '/home');
            exit();
        }
    }
}
?>