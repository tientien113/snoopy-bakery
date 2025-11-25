<?php
class AdminMiddleware {
    
    public function handle() {
        if (!AuthHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        if (!AuthHelper::isAdmin()) {
            header('Location: ' . BASE_URL . '/home');
            exit();
        }
    }
}
?>