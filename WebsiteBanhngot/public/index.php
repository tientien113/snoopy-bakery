<?php
/**
 * Entry Point - Tiệm bánh SNOOPY
 * XAMPP/htdocs/WebsiteBanhngot/public/index.php
 */

// Start session
session_start();

// Define basic constants FIRST - trước khi load config
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Define BASE_URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . "://" . $host . str_replace('/public', '', $script_path);
define('BASE_URL', rtrim($base_url, '/'));

// Load configuration
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/config/constants.php'; // Load constants sau khi đã định nghĩa BASE_PATH

// Auto-load classes
function autoLoadClass($className) {
    $file = APP_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('autoLoadClass');

// Get URL parameters
$url = $_GET['url'] ?? 'home';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Route handling
$controllerName = ucfirst($urlParts[0] ?? 'home') . 'Controller';
$action = $urlParts[1] ?? 'index';
$params = array_slice($urlParts, 2);

// Check if controller exists
$controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

try {
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $action)) {
                call_user_func_array([$controller, $action], $params);
            } else {
                // 404 - Action not found
                http_response_code(HTTP_NOT_FOUND);
                $this->showErrorPage("Action không tồn tại: $action");
            }
        } else {
            // 404 - Controller not found
            http_response_code(HTTP_NOT_FOUND);
            $this->showErrorPage("Controller không tồn tại: $controllerName");
        }
    } else {
        // 404 - Controller file not found
        http_response_code(HTTP_NOT_FOUND);
        $this->showErrorPage("Trang không tồn tại");
    }
} catch (Exception $e) {
    http_response_code(HTTP_INTERNAL_ERROR);
    $this->showErrorPage("Lỗi hệ thống: " . $e->getMessage());
}

function showErrorPage($message) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Lỗi - " . BRAND_NAME . "</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            .error-container { margin-top: 100px; }
        </style>
    </head>
    <body>
        <nav class='navbar navbar-dark bg-dark'>
            <div class='container'>
                <a class='navbar-brand' href='" . BASE_URL . "'>" . BRAND_NAME . "</a>
            </div>
        </nav>
        <div class='container error-container'>
            <div class='row justify-content-center'>
                <div class='col-md-6 text-center'>
                    <h1>😕</h1>
                    <h2>Đã xảy ra lỗi</h2>
                    <p class='text-muted'>$message</p>
                    <a href='" . BASE_URL . "' class='btn btn-primary'>Về trang chủ</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
}
?>