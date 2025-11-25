<?php
class FormatHelper {
    
    // Format currency
    public static function formatCurrency($amount) {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
    
    // Format date
    public static function formatDate($date, $format = 'd/m/Y') {
        if (empty($date)) return '';
        $datetime = new DateTime($date);
        return $datetime->format($format);
    }
    
    // Format datetime
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i') {
        if (empty($datetime)) return '';
        $dt = new DateTime($datetime);
        return $dt->format($format);
    }
    
    // Format order status
    public static function formatOrderStatus($status) {
        $statuses = [
            'pending' => ['text' => 'Chờ xác nhận', 'class' => 'warning'],
            'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'info'],
            'ready' => ['text' => 'Sẵn sàng lấy', 'class' => 'primary'],
            'completed' => ['text' => 'Hoàn thành', 'class' => 'success'],
            'cancelled' => ['text' => 'Đã hủy', 'class' => 'danger']
        ];
        
        return $statuses[$status] ?? ['text' => 'Không xác định', 'class' => 'secondary'];
    }
    
    // Format payment status
    public static function formatPaymentStatus($status) {
        $statuses = [
            'pending' => ['text' => 'Chờ thanh toán', 'class' => 'warning'],
            'paid' => ['text' => 'Đã thanh toán', 'class' => 'success'],
            'failed' => ['text' => 'Thất bại', 'class' => 'danger'],
            'refunded' => ['text' => 'Đã hoàn tiền', 'class' => 'info']
        ];
        
        return $statuses[$status] ?? ['text' => 'Không xác định', 'class' => 'secondary'];
    }
    
    // Format payment method
    public static function formatPaymentMethod($method) {
        $methods = [
            'cash_on_pickup' => 'Tiền mặt khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'momo' => 'Ví MoMo',
            'vnpay' => 'VNPay'
        ];
        
        return $methods[$method] ?? 'Không xác định';
    }
    
    // Truncate text
    public static function truncateText($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }
    
    // Generate order code
    public static function generateOrderCode() {
        return 'DH' . date('Ymd') . strtoupper(uniqid());
    }
    
    // Calculate discount percentage
    public static function calculateDiscountPercent($price, $discountPrice) {
        if ($discountPrice && $price > 0) {
            return round((($price - $discountPrice) / $price) * 100);
        }
        return 0;
    }
    
    // Format file size
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
?>