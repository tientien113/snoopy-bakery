<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/orders/history">Lịch sử đơn hàng</a></li>
                    <li class="breadcrumb-item active">Đơn hàng <?php echo $order['order_code']; ?></li>
                </ol>
            </nav>
            
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-invoice"></i> Chi tiết đơn hàng</h4>
                    <span class="badge bg-light text-dark h5 mb-0"><?php echo $order['order_code']; ?></span>
                </div>
                <div class="card-body">
                    <!-- Order Status -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <?php 
                            $status_steps = [
                                'pending' => ['icon' => 'clock', 'text' => 'Chờ xác nhận', 'class' => 'secondary'],
                                'confirmed' => ['icon' => 'check', 'text' => 'Đã xác nhận', 'class' => 'info'],
                                'ready' => ['icon' => 'box', 'text' => 'Sẵn sàng', 'class' => 'primary'],
                                'completed' => ['icon' => 'check-double', 'text' => 'Hoàn thành', 'class' => 'success']
                            ];
                            
                            $current_status_index = array_search($order['status'], array_keys($status_steps));
                            ?>
                            
                            <div class="progress-container">
                                <?php foreach ($status_steps as $status => $step): ?>
                                <?php 
                                $step_index = array_search($status, array_keys($status_steps));
                                $is_active = $step_index <= $current_status_index;
                                $is_current = $status === $order['status'];
                                ?>
                                <div class="progress-step <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_current ? 'current' : ''; ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-<?php echo $step['icon']; ?>"></i>
                                    </div>
                                    <div class="step-text"><?php echo $step['text']; ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($order['status'] == 'pending'): ?>
                            <button class="btn btn-outline-danger cancel-order" 
                                    data-order-id="<?php echo $order['id']; ?>"
                                    data-order-code="<?php echo $order['order_code']; ?>">
                                <i class="fas fa-times"></i> Hủy đơn hàng
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Order Items -->
                        <div class="col-md-8">
                            <h5 class="mb-3"><i class="fas fa-boxes"></i> Sản phẩm</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th width="100">Số lượng</th>
                                            <th width="120">Đơn giá</th>
                                            <th width="120">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo BASE_URL . '/public/' . ($item['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                                                         alt="<?php echo $item['name']; ?>" 
                                                         class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                                            <td class="text-end"><?php echo number_format($item['unit_price']); ?>₫</td>
                                            <td class="text-end fw-bold">
                                                <?php echo number_format($item['unit_price'] * $item['quantity']); ?>₫
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                            <td class="text-end fw-bold h5 text-danger"><?php echo number_format($order['final_price']); ?>₫</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Order Information -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Ngày đặt:</strong><br>
                                        <?php echo date('H:i d/m/Y', strtotime($order['created_at'])); ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Khách hàng:</strong><br>
                                        <?php echo $order['customer_name']; ?><br>
                                        <?php echo $order['customer_phone']; ?><br>
                                        <?php echo $order['customer_email']; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Thời gian lấy hàng:</strong><br>
                                        <?php echo date('H:i d/m/Y', strtotime($order['pickup_time'])); ?>
                                    </div>
                                    
                                    <?php if ($order['pickup_note']): ?>
                                    <div class="mb-3">
                                        <strong>Ghi chú:</strong><br>
                                        <?php echo nl2br($order['pickup_note']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($payment): ?>
                                    <div class="mb-3">
                                        <strong>Thanh toán:</strong><br>
                                        <?php 
                                        $payment_methods = [
                                            'cash_on_pickup' => 'Tiền mặt khi nhận hàng',
                                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                                            'momo' => 'Ví MoMo',
                                            'vnpay' => 'VNPay'
                                        ];
                                        $payment_status = [
                                            'pending' => ['text' => 'Chờ thanh toán', 'class' => 'warning'],
                                            'paid' => ['text' => 'Đã thanh toán', 'class' => 'success'],
                                            'failed' => ['text' => 'Thất bại', 'class' => 'danger'],
                                            'refunded' => ['text' => 'Đã hoàn tiền', 'class' => 'info']
                                        ];
                                        ?>
                                        <?php echo $payment_methods[$payment['method']]; ?><br>
                                        <span class="badge bg-<?php echo $payment_status[$payment['status']]['class']; ?>">
                                            <?php echo $payment_status[$payment['status']]['text']; ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
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
.progress-container {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin: 20px 0;
}

.progress-container::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 4px;
    background: #e9ecef;
    z-index: 1;
}

.progress-step {
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    border: 3px solid #e9ecef;
}

.step-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.progress-step.active .step-icon {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.progress-step.active .step-text {
    color: #0d6efd;
    font-weight: bold;
}

.progress-step.current .step-icon {
    background: #198754;
    border-color: #198754;
    color: white;
}

.progress-step.current .step-text {
    color: #198754;
    font-weight: bold;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Cancel order button
    const cancelBtn = document.querySelector('.cancel-order');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
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
    }
});
</script>