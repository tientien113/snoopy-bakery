<?php
class UploadHelper {
    
    // Upload product image
    public static function uploadProductImage($file, $productId = null) {
        return self::uploadFile($file, UPLOAD_PRODUCT_PATH, [
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'prefix' => 'product_' . ($productId ? $productId . '_' : '')
        ]);
    }
    
    // Upload user avatar
    public static function uploadAvatar($file, $userId = null) {
        return self::uploadFile($file, UPLOAD_AVATAR_PATH, [
            'max_size' => 2 * 1024 * 1024, // 2MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'prefix' => 'avatar_' . ($userId ? $userId . '_' : '')
        ]);
    }
    
    // Generic file upload method
    private static function uploadFile($file, $uploadPath, $options = []) {
        $defaults = [
            'max_size' => 5 * 1024 * 1024,
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'prefix' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => self::getUploadError($file['error'])];
        }
        
        // Check file size
        if ($file['size'] > $options['max_size']) {
            return ['success' => false, 'error' => 'File quá lớn. Kích thước tối đa: ' . ($options['max_size'] / 1024 / 1024) . 'MB'];
        }
        
        // Check file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $options['allowed_types'])) {
            return ['success' => false, 'error' => 'Loại file không được hỗ trợ. Chỉ chấp nhận: ' . implode(', ', $options['allowed_types'])];
        }
        
        // Create upload directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Generate unique filename
        $filename = $options['prefix'] . uniqid() . '_' . time() . '.' . $fileExtension;
        $filepath = $uploadPath . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'url' => str_replace(PUBLIC_PATH, BASE_URL, $filepath)
            ];
        } else {
            return ['success' => false, 'error' => 'Không thể tải file lên'];
        }
    }
    
    // Delete file
    public static function deleteFile($filepath) {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    // Get upload error message
    private static function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File vượt quá kích thước cho phép',
            UPLOAD_ERR_FORM_SIZE => 'File vượt quá kích thước form',
            UPLOAD_ERR_PARTIAL => 'File chỉ được tải lên một phần',
            UPLOAD_ERR_NO_FILE => 'Không có file được tải lên',
            UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
            UPLOAD_ERR_EXTENSION => 'PHP extension dừng việc tải file'
        ];
        
        return $errors[$errorCode] ?? 'Lỗi không xác định';
    }
}
?>