<?php
class HomeController {
    public function index() {
        // Get featured products
        $featured_products = $this->getSampleProducts();
        
        // Data for view
        $data = [
            'title' => BRAND_NAME . ' - ' . BRAND_SLOGAN,
            'BASE_URL' => BASE_URL,
            'featured_products' => $featured_products,
            'brand_name' => BRAND_NAME,
            'brand_slogan' => BRAND_SLOGAN,
            'brand_phone' => BRAND_PHONE,
            'brand_address' => BRAND_ADDRESS,
            'brand_open_hours' => BRAND_OPEN_HOURS
        ];
        
        // Load view
        $this->loadView('home/index', $data);
    }
    
    private function getSampleProducts() {
        // Return sample products data
        return [
            [
                'id' => 1,
                'name' => 'Bánh Tiramisu',
                'price' => 120000,
                'discount_price' => 99000,
                'description' => 'Bánh Tiramisu thơm ngon Ý với lớp cà phê đậm đà',
                'image' => 'default-product.jpg',
                'quantity' => 15
            ],
            [
                'id' => 2,
                'name' => 'Bánh Chocolate',
                'price' => 95000,
                'discount_price' => null,
                'description' => 'Bánh Chocolate đậm đà với lớp ganache bóng mượt',
                'image' => 'default-product.jpg',
                'quantity' => 20
            ],
            [
                'id' => 3,
                'name' => 'Bánh Kem Sinh Nhật',
                'price' => 250000,
                'discount_price' => 199000,
                'description' => 'Bánh kem sinh nhật trang trí theo yêu cầu',
                'image' => 'default-product.jpg',
                'quantity' => 8
            ],
            [
                'id' => 4,
                'name' => 'Bánh Mì Baguette',
                'price' => 20000,
                'discount_price' => 15000,
                'description' => 'Bánh mì Pháp giòn tan, thơm ngon',
                'image' => 'default-product.jpg',
                'quantity' => 30
            ]
        ];
    }
    
    private function loadView($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // View file path
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // Fallback view
            $this->showFallbackView($data);
        }
    }
    
    private function showFallbackView($data) {
        extract($data);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>$title</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
        </head>
        <body>
            <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
                <div class='container'>
                    <a class='navbar-brand' href='$BASE_URL'>
                        <i class='fas fa-birthday-cake'></i> $brand_name
                    </a>
                </div>
            </nav>
            
            <div class='container mt-4'>
                <div class='jumbotron bg-light p-5 rounded'>
                    <h1>Chào mừng đến với $brand_name!</h1>
                    <p class='lead'>$brand_slogan</p>
                    
                    <div class='row mt-4'>
                        <div class='col-md-6'>
                            <h4>Thông tin liên hệ:</h4>
                            <p><i class='fas fa-phone'></i> $brand_phone</p>
                            <p><i class='fas fa-map-marker-alt'></i> $brand_address</p>
                            <p><i class='fas fa-clock'></i> $brand_open_hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>