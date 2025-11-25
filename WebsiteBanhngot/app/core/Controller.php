<?php
class Controller {
    protected $db;
    protected $model;
    
    public function __construct() {
        $this->db = new Database();
    }

    // Load model
    public function model($model) {
        require_once APP_PATH . '/models/' . $model . '.php';
        return new $model();
    }

    // Load view - FIXED: Pass BASE_URL to all views
    public function view($view, $data = []) {
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        // Always make BASE_URL available in views
        $data['BASE_URL'] = BASE_URL;
        
        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            die('View does not exist: ' . $view);
        }
    }

    // Redirect
    public function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }

    // JSON response
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // Get POST data
    protected function getPostData() {
        return $_POST;
    }

    // Get GET data
    protected function getQueryParams() {
        return $_GET;
    }

    // Check if request is AJAX
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>