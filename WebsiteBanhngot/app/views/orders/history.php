<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-history"></i> Lịch sử đơn hàng</h2>
            
            <?php if (!empty($orders)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn hàng</th>
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
                                    </td>
                                    <td>
                                        <?php echo date('H:i d/m/Y', strtotime($order['created_at'])); ?>
                                        <?php if ($order['pickup_time']): ?>
                                            <br><small class="text-muted">Lấy: <?php echo date('H:i d/m', strtotime($order['pickup_time'])); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        <?php echo number_format($order['final_price']); ?>₫
                                    </td>
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
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/orders/detail/<?php echo $order['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
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
                                <a class="page-link" href="<?php echo BASE_URL; ?>/orders/history?page=<?php echo $current_page - 1; ?>">
                                    &laquo; Trước
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo BASE_URL; ?>/orders/history?page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo BASE_URL; ?>/orders/history?page=<?php echo $current_page + 1; ?>">
                                    Tiếp &raquo;
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có đơn hàng nào</h4>
                <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên.</p>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                </a>
            </div>
            <?php endif; ?>
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
                        window.location.href = data.redirect;
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