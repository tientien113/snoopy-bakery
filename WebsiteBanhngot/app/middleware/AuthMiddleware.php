<?php
class AuthMiddleware {
    
    public function handle() {
        AuthHelper::requireLogin();
        AuthHelper::checkSessionTimeout();
    }
}
?>