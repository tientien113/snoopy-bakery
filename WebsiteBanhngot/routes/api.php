<?php
// API routes for AJAX requests
$apiRoutes = [
    // Cart API
    'api/cart/add' => 'CartController/addToCartApi',
    'api/cart/update' => 'CartController/updateCartApi',
    'api/cart/remove' => 'CartController/removeFromCartApi',
    'api/cart/count' => 'CartController/getCartCountApi',
    'api/cart/items' => 'CartController/getCartItemsApi',
    
    // Product API
    'api/products/search' => 'ProductController/searchApi',
    'api/products/category/(:num)' => 'ProductController/getByCategoryApi',
    'api/products/(:num)/stock' => 'ProductController/checkStockApi',
    
    // Order API
    'api/orders/status' => 'OrderController/checkOrderStatusApi',
    'api/orders/(:num)/track' => 'OrderController/trackOrderApi',
    
    // User API
    'api/user/profile' => 'UserController/updateProfileApi',
    'api/user/check-email' => 'UserController/checkEmailApi',
    
    // Admin API
    'api/admin/dashboard/stats' => 'AdminController/getDashboardStatsApi',
    'api/admin/orders/chart' => 'AdminController/getOrdersChartApi',
    'api/admin/products/update-status' => 'AdminController/updateProductStatusApi',
    'api/admin/orders/update-status' => 'AdminController/updateOrderStatusApi',
    
    // Upload API
    'api/upload/image' => 'UploadController/uploadImageApi',
];

return $apiRoutes;
?>