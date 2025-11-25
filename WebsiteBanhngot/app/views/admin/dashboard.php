<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt"></i> Bảng điều khiển</h2>
                <span class="text-muted"><?php echo date('d/m/Y'); ?></span>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tổng đơn hàng
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['total_orders']); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Doanh thu hôm nay
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['today_revenue']); ?>₫
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Khách hàng
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['total_customers']); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Sản phẩm
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($stats['total_products']); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_orders)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Khách hàng</th>
                                            <th>Ngày đặt</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/admin/orders/detail/<?php echo $order['id']; ?>">
                                                    <?php echo $order['order_code']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $order['customer_name']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                            <td class="text-danger"><?php echo number_format($order['final_price']); ?>₫</td>
                                            <td>
                                                <?php 
                                                $status_badge = [
                                                    'pending' => 'secondary',
                                                    'confirmed' => 'info', 
                                                    'ready' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $status_text = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'ready' => 'Sẵn sàng',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $status_badge[$order['status']]; ?>">
                                                    <?php echo $status_text[$order['status']]; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>/admin/orders" class="btn btn-primary btn-sm">
                                    Xem tất cả đơn hàng
                                </a>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center py-3">Chưa có đơn hàng nào</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Products -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Sản phẩm sắp hết hàng</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($low_stock_products)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($low_stock_products as $product): ?>
                                <a href="<?php echo BASE_URL; ?>/admin/products/edit/<?php echo $product['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $product['name']; ?></h6>
                                        <span class="badge bg-danger"><?php echo $product['quantity']; ?></span>
                                    </div>
                                    <small class="text-muted">Còn <?php echo $product['quantity']; ?> sản phẩm</small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>/admin/products" class="btn btn-warning btn-sm">
                                    Quản lý kho
                                </a>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center py-3">Tất cả sản phẩm đều đủ hàng</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card shadow mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-bolt"></i> Thao tác nhanh</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/products/create" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/categories/create" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-folder-plus"></i> Thêm danh mục
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/orders" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-clipboard-list"></i> Xử lý đơn hàng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df!important; }
.border-left-success { border-left: 0.25rem solid #1cc88a!important; }
.border-left-info { border-left: 0.25rem solid #36b9cc!important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e!important; }
</style>