<?php
/**
 * Main Configuration - Tiệm bánh SNOOPY
 * XAMPP/htdocs setup
 */

class AppConfig {
    // Application settings
    const APP_NAME = 'SNOOPY Bakery';
    const APP_VERSION = '1.0.0';
    const APP_DESCRIPTION = 'Tiệm bánh ngọt SNOOPY - Hương vị ngọt ngào cho mọi nhà';
    
    // Base URLs - Đã điều chỉnh cho XAMPP
    const BASE_URL = 'http://localhost/WebsiteBanhngot';
    const PUBLIC_URL = 'http://localhost/WebsiteBanhngot/public';
    
    // Path settings - Đã điều chỉnh cho XAMPP/htdocs
    const ROOT_PATH = 'C:/xampp/htdocs/WebsiteBanhngot';
    const APP_PATH = 'C:/xampp/htdocs/WebsiteBanhngot/app';
    const PUBLIC_PATH = 'C:/xampp/htdocs/WebsiteBanhngot/public';
    const UPLOAD_PATH = 'C:/xampp/htdocs/WebsiteBanhngot/public/uploads';
    
    // Session settings
    const SESSION_NAME = 'snoopy_bakery';
    const SESSION_LIFETIME = 86400; // 24 hours
    
    // Security settings
    const CSRF_TOKEN_NAME = 'csrf_token';
    const PASSWORD_COST = 12;
    
    // File upload settings
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Pagination
    const ITEMS_PER_PAGE = 12;
    const ADMIN_ITEMS_PER_PAGE = 10;
    
    // Order settings
    const ORDER_CODE_PREFIX = 'SNP';
    const MIN_ORDER_AMOUNT = 0; // No minimum order amount
    
    /**
     * Get application environment
     */
    public static function getEnvironment() {
        return $_SERVER['SERVER_NAME'] == 'localhost' ? 'development' : 'production';
    }
    
    /**
     * Check if running in development mode
     */
    public static function isDevelopment() {
        return self::getEnvironment() === 'development';
    }
    
    /**
     * Get upload path for specific type
     */
    public static function getUploadPath($type = 'products') {
        $path = self::UPLOAD_PATH . '/' . $type;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }
    
    /**
     * Get upload URL for specific type
     */
    public static function getUploadUrl($type = 'products') {
        return self::PUBLIC_URL . '/uploads/' . $type;
    }
    
    /**
     * Get relative path from root
     */
    public static function getRelativePath($absolutePath) {
        return str_replace(self::ROOT_PATH . '/', '', $absolutePath);
    }
}
?>