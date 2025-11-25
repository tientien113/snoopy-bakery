<?php
/**
 * Application Constants - Tiệm bánh SNOOPY
 */

// User roles
define('ROLE_ADMIN', 1);
define('ROLE_MANAGER', 2);
define('ROLE_STAFF', 3);
define('ROLE_CUSTOMER', 4);

// Order statuses
define('ORDER_PENDING', 'pending');
define('ORDER_CONFIRMED', 'confirmed');
define('ORDER_READY', 'ready');
define('ORDER_COMPLETED', 'completed');
define('ORDER_CANCELLED', 'cancelled');

// Payment statuses
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_PAID', 'paid');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_REFUNDED', 'refunded');

// Payment methods
define('PAYMENT_CASH_PICKUP', 'cash_on_pickup');
define('PAYMENT_BANK_TRANSFER', 'bank_transfer');
define('PAYMENT_MOMO', 'momo');
define('PAYMENT_VNPAY', 'vnpay');

// Product status
define('PRODUCT_ACTIVE', 1);
define('PRODUCT_INACTIVE', 0);

// User status
define('USER_ACTIVE', 1);
define('USER_INACTIVE', 0);

// Inventory change types
define('INVENTORY_IMPORT', 'import');
define('INVENTORY_SOLD', 'sold');
define('INVENTORY_ADJUST', 'adjust');
define('INVENTORY_RETURN', 'return');

// Session keys
define('SESSION_USER_ID', 'user_id');
define('SESSION_USER_ROLE', 'user_role');
define('SESSION_USER_NAME', 'user_name');
define('SESSION_CART_COUNT', 'cart_count');
define('SESSION_FLASH_MESSAGE', 'flash_message');

// Flash message types
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');

// HTTP status codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_INTERNAL_ERROR', 500);

// Validation rules
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_PRODUCT_NAME', 200);
define('MAX_CATEGORY_NAME', 120);
define('MAX_USER_NAME', 150);

// Date formats
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');
define('DATETIME_DB_FORMAT', 'Y-m-d H:i:s');

/**
 * Error messages
 */
class ErrorMessages {
    const DB_CONNECTION = 'Lỗi kết nối database. Vui lòng thử lại sau.';
    const UNAUTHORIZED = 'Bạn không có quyền truy cập trang này.';
    const NOT_FOUND = 'Trang không tồn tại.';
    const METHOD_NOT_ALLOWED = 'Phương thức không được hỗ trợ.';
    const VALIDATION_ERROR = 'Dữ liệu không hợp lệ.';
    const UPLOAD_ERROR = 'Lỗi tải file lên.';
    const ORDER_ERROR = 'Lỗi xử lý đơn hàng.';
}

/**
 * Success messages
 */
class SuccessMessages {
    const LOGIN_SUCCESS = 'Đăng nhập thành công!';
    const REGISTER_SUCCESS = 'Đăng ký thành công!';
    const PRODUCT_ADDED = 'Thêm sản phẩm thành công!';
    const PRODUCT_UPDATED = 'Cập nhật sản phẩm thành công!';
    const PRODUCT_DELETED = 'Xóa sản phẩm thành công!';
    const ORDER_CREATED = 'Đặt hàng thành công!';
    const ORDER_UPDATED = 'Cập nhật đơn hàng thành công!';
    const PROFILE_UPDATED = 'Cập nhật thông tin thành công!';
}

// Brand information
define('BRAND_NAME', 'SNOOPY Bakery');
define('BRAND_SLOGAN', 'Hương vị ngọt ngào cho mọi nhà');
define('BRAND_EMAIL', 'info@snoopy-bakery.com');
define('BRAND_PHONE', '0123 456 789');
define('BRAND_ADDRESS', '123 Đường ABC, Quận XYZ, Đà Nẵng');
define('BRAND_OPEN_HOURS', '7:00 - 21:00 hàng ngày');

// Social media
define('SOCIAL_FACEBOOK', 'https://facebook.com/snoopybakery');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/snoopybakery');
define('SOCIAL_ZALO', 'https://zalo.me/0123456789');
?>