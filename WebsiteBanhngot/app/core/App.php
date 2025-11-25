<?php
class App {
    public function __construct() {
        echo "=== DEBUG: App Constructor Started ===<br>";
        
        try {
            // Get URL
            $url = $this->getUrl();
            echo "URL: " . print_r($url, true) . "<br>";
            
            // Set default controller and method
            $controllerName = 'Home';
            $method = 'index';
            
            // Parse URL
            if (!empty($url[0])) {
                $controllerName = ucfirst($url[0]);
            }
            
            if (!empty($url[1])) {
                $method = $url[1];
            }
            
            echo "Controller: $controllerName, Method: $method<br>";
            
            // Load controller file
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . 'Controller.php';
            echo "Controller file: $controllerFile<br>";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                $controllerClassName = $controllerName . 'Controller';
                echo "Controller class: $controllerClassName<br>";
                
                if (class_exists($controllerClassName)) {
                    $controller = new $controllerClassName();
                    echo "Controller instantiated<br>";
                    
                    if (method_exists($controller, $method)) {
                        echo "Calling method: $method<br>";
                        call_user_func([$controller, $method]);
                    } else {
                        throw new Exception("Method $method not found in $controllerClassName");
                    }
                } else {
                    throw new Exception("Class $controllerClassName not found");
                }
            } else {
                throw new Exception("Controller file not found: $controllerFile");
            }
            
        } catch (Exception $e) {
            echo "<h1>Application Error</h1>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
            echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        }
        
        echo "=== DEBUG: App Constructor Ended ===<br>";
    }
    
    private function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
?>