<?php
class ValidationHelper {
    
    // Validate email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validate phone number (Vietnamese format)
    public static function validatePhone($phone) {
        return preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/', $phone);
    }
    
    // Validate password strength
    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }
    
    // Sanitize string
    public static function sanitizeString($string) {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }
    
    // Sanitize array
    public static function sanitizeArray($array) {
        return array_map([self::class, 'sanitizeString'], $array);
    }
    
    // Validate required fields
    public static function validateRequired($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "Trường này là bắt buộc";
            }
        }
        
        return $errors;
    }
    
    // Validate numeric range
    public static function validateNumber($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return true;
    }
    
    // Validate price
    public static function validatePrice($price) {
        return is_numeric($price) && $price >= 0;
    }
    
    // Validate quantity
    public static function validateQuantity($quantity) {
        return is_numeric($quantity) && $quantity >= 0 && $quantity == (int)$quantity;
    }
    
    // Validate date
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    // Get validation errors as HTML
    public static function getErrorsHTML($errors) {
        if (empty($errors)) return '';
        
        $html = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $html .= '<li>' . $error . '</li>';
        }
        $html .= '</ul></div>';
        
        return $html;
    }
}
?>