<?php
class Session {
    
    // Set session value
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    // Get session value
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    // Check if session key exists
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    // Remove session value
    public static function remove($key) {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }
    
    // Set flash message
    public static function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    // Get flash message
    public static function getFlash($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        self::removeFlash($key);
        return $message;
    }
    
    // Check if flash message exists
    public static function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
    
    // Remove flash message
    public static function removeFlash($key) {
        if (self::hasFlash($key)) {
            unset($_SESSION['flash'][$key]);
        }
    }
    
    // Clear all flash messages
    public static function clearFlash() {
        unset($_SESSION['flash']);
    }
    
    // Destroy session
    public static function destroy() {
        session_unset();
        session_destroy();
    }
}
?>