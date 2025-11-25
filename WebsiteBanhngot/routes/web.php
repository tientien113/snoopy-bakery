<?php
// Authentication routes
$routes['login'] = 'AuthController/login';
$routes['register'] = 'AuthController/register';
$routes['logout'] = 'AuthController/logout';

// Home page
$routes[''] = 'HomeController/index';
$routes['home'] = 'HomeController/index';

// Product routes
$routes['products'] = 'ProductController/index';
$routes['products/list'] = 'ProductController/list';
$routes['products/detail/(:num)'] = 'ProductController/detail/$1';
$routes['products/search'] = 'ProductController/search';
$routes['products/category/(:num)'] = 'ProductController/category/$1';

// Cart routes
$routes['cart'] = 'CartController/index';
$routes['cart/add'] = 'CartController/add';
$routes['cart/update'] = 'CartController/update';
$routes['cart/remove/(:num)'] = 'CartController/remove/$1';
$routes['cart/clear'] = 'CartController/clear';

// Order routes
$routes['checkout'] = 'OrderController/checkout';
$routes['orders/create'] = 'OrderController/create';
$routes['orders/success'] = 'OrderController/success';
$routes['orders/history'] = 'OrderController/history';
$routes['orders/detail/(:num)'] = 'OrderController/detail/$1';

// User routes
$routes['profile'] = 'UserController/profile';
$routes['profile/update'] = 'UserController/update';

// Admin routes
$routes['admin'] = 'AdminController/dashboard';
$routes['admin/dashboard'] = 'AdminController/dashboard';

// Admin products
$routes['admin/products'] = 'AdminController/products';
$routes['admin/products/list'] = 'AdminController/productsList';
$routes['admin/products/create'] = 'AdminController/createProduct';
$routes['admin/products/edit/(:num)'] = 'AdminController/editProduct/$1';
$routes['admin/products/delete/(:num)'] = 'AdminController/deleteProduct/$1';

// Admin categories
$routes['admin/categories'] = 'AdminController/categories';
$routes['admin/categories/create'] = 'AdminController/createCategory';
$routes['admin/categories/edit/(:num)'] = 'AdminController/editCategory/$1';
$routes['admin/categories/delete/(:num)'] = 'AdminController/deleteCategory/$1';

// Admin users
$routes['admin/users'] = 'AdminController/users';
$routes['admin/users/create'] = 'AdminController/createUser';
$routes['admin/users/edit/(:num)'] = 'AdminController/editUser/$1';
$routes['admin/users/delete/(:num)'] = 'AdminController/deleteUser/$1';

// Admi  n orders
$routes['admin/orders'] = 'AdminController/orders';
$routes['admin/orders/detail/(:num)'] = 'AdminController/orderDetail/$1';
$routes['admin/orders/process/(:num)'] = 'AdminController/processOrder/$1';
$routes['admin/orders/update-status'] = 'AdminController/updateOrderStatus';

// Admin reports
$routes['admin/reports'] = 'AdminController/reports';

return $routes;
?>