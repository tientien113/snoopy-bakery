<?php
/**
 * Database Configuration - Tiệm bánh SNOOPY
 */

class DatabaseConfig {
    const DB_HOST = 'localhost';
    const DB_NAME = 'website_banhngot';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';

    // Database tables
    const TABLE_USERS = 'users';
    const TABLE_ROLES = 'roles';
    const TABLE_PRODUCTS = 'products';
    const TABLE_CATEGORIES = 'categories';
    const TABLE_CART = 'cart';
    const TABLE_ORDERS = 'orders';
    const TABLE_ORDER_ITEMS = 'order_items';
    const TABLE_PAYMENTS = 'payments';
    const TABLE_REVIEWS = 'reviews';
    const TABLE_PRODUCT_IMAGES = 'product_images';
    const TABLE_ORDER_STATUS_HISTORY = 'order_status_history';
    const TABLE_INVENTORY_LOGS = 'inventory_logs';
    const TABLE_ACTIVITY_LOGS = 'activity_logs';


    public static function getConnection() {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            $pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }
}
?>