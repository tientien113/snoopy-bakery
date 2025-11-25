<?php
class GuestMiddleware {
    
    public function handle() {
        AuthHelper::requireGuest();
    }
}
?>