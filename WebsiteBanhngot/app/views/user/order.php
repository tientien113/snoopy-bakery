<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-md-3">
            <!-- User Sidebar -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user-circle"></i> Tài khoản</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo BASE_URL; ?>/user/profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>Thông tin cá nhân
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/orders" class="list-group-item list-group-item-action active">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                    </a>
                    <a href="<?php echo BASE_URL; ?>/orders/history" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                    </a>
                </div>
            </div>
            
            <!-- Order Filters -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Lọc đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>/user/orders" 
                           class="list-group-item list-group-item-action <?php echo !$current_status ? 'active' : ''; ?>">
                            Tất cả đơn hàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders?status=pending" 
                           class="list-group-item list-group-item-action <?php echo $current_status == 'pending' ? 'active' : ''; ?>">
                            Chờ xác nhận
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders?status=confirmed" 
                           class="list-group-item list-group-item-action <?php echo $current_status == 'confirmed' ? 'active' : ''; ?>">
                            Đã xác nhận
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders?status=ready" 
                           class="list-group-item list-group-item-action <?php echo $current_status == 'ready' ? 'active' : ''; ?>">
                            Sẵn sàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders?status=completed" 
                           class="list-group-item list-group-item-action <?php echo $current_status == 'completed' ? 'active' : ''; ?>">
                            Hoàn thành
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders?status=cancelled" 
                           class="list-group-item list-group-item-action <?php echo $current_status == 'cancelled' ? 'active' : ''; ?>">
                            Đã hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-shopping-bag"></i> Đơn hàng của tôi</h4>
                    <span class="badge bg-light text-dark"><?php echo $total_orders; ?> đơn hàng</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $order['order_code']; ?></strong>
                                        <?php if ($order['pickup_time']): ?>
                                            <br><small class="text-muted">Lấy: <?php echo date('H:i d/m', strtotime($order['pickup_time'])); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td class="fw-bold text-danger"><?php echo number_format($order['final_price']); ?>₫</td>
                                    <td>
                                        <?php 
                                        $status_badge = [
                                            'pending' => ['class' => 'secondary', 'text' => 'Chờ xác nhận'],
                                            'confirmed' => ['class' => 'info', 'text' => 'Đã xác nhận'],
                                            'ready' => ['class' => 'primary', 'text' => 'Sẵn sàng'],
                                            'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
                                            'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy']
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $status_badge[$order['status']]['class']; ?>">
                                            <?php echo $status_badge[$order['status']]['text']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/orders/detail/<?php echo $order['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        
                                        <?php if ($order['status'] == 'pending'): ?>
                                        <button class="btn btn-outline-danger btn-sm cancel-order" 
                                                data-order-id="<?php echo $order['id']; ?>"
                                                data-order-code="<?php echo $order['order_code']; ?>">
                                            <i class="fas fa-times"></i> Hủy
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/user/orders?page=<?php echo $current_page - 1; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?>">
                                    &laquo; Trước
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/user/orders?page=<?php echo $i; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/user/orders?page=<?php echo $current_page + 1; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?>">
                                    Tiếp &raquo;
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">
                            <?php if ($current_status): ?>
                                Không có đơn hàng <?php echo $status_badge[$current_status]['text']; ?> nào
                            <?php else: ?>
                                Chưa có đơn hàng nào
                            <?php endif; ?>
                        </h5>
                        <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Cancel order buttons
    document.querySelectorAll('.cancel-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderCode = this.getAttribute('data-order-code');
            
            if (confirm(`Bạn có chắc muốn hủy đơn hàng ${orderCode}?`)) {
                fetch('<?php echo BASE_URL; ?>/orders/cancel/' + orderId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã hủy đơn hàng thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi hủy đơn hàng!');
                });
            }
        });
    });
});
</script>