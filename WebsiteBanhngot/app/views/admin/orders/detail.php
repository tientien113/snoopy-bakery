<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-invoice"></i> Chi tiết đơn hàng</h2>
                <div>
                    <a href="<?php echo BASE_URL; ?>/admin/orders" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="badge bg-light text-dark h5 mb-0"><?php echo $order['order_code']; ?></span>
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-md-8">
                    <!-- Order Status -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-tasks"></i> Trạng thái đơn hàng</h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            $status_steps = [
                                'pending' => ['icon' => 'clock', 'text' => 'Chờ xác nhận', 'class' => 'secondary'],
                                'confirmed' => ['icon' => 'check', 'text' => 'Đã xác nhận', 'class' => 'info'],
                                'ready' => ['icon' => 'box', 'text' => 'Sẵn sàng', 'class' => 'primary'],
                                'completed' => ['icon' => 'check-double', 'text' => 'Hoàn thành', 'class' => 'success']
                            ];
                            
                            $current_status_index = array_search($order['status'], array_keys($status_steps));
                            ?>
                            
                            <div class="progress-container mb-4">
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

                            <!-- Status Update Form -->
                            <?php if ($order['status'] != 'completed' && $order['status'] != 'cancelled'): ?>
                            <form id="statusForm" class="mt-4">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <select class="form-select" name="status" required>
                                            <option value="">Chọn trạng thái mới</option>
                                            <?php foreach ($status_steps as $status => $step): ?>
                                                <?php if ($status != $order['status']): ?>
                                                <option value="<?php echo $status; ?>"><?php echo $step['text']; ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-sync-alt"></i> Cập nhật
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <textarea class="form-control" name="note" rows="2" placeholder="Ghi chú (tùy chọn)"></textarea>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-boxes"></i> Sản phẩm</h6>
                        </div>
                        <div class="card-body">
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
                    </div>

                    <!-- Status History -->
                    <?php if (!empty($status_history)): ?>
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Lịch sử trạng thái</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($status_history as $history): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <strong>
                                                <?php 
                                                $status_text = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'confirmed' => 'Đã xác nhận', 
                                                    'ready' => 'Sẵn sàng',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                echo $status_text[$history['new_status']];
                                                ?>
                                            </strong>
                                            <small class="text-muted"><?php echo date('H:i d/m/Y', strtotime($history['created_at'])); ?></small>
                                        </div>
                                        <?php if ($history['changed_by_name']): ?>
                                        <small class="text-muted">Thay đổi bởi: <?php echo $history['changed_by_name']; ?></small>
                                        <?php endif; ?>
                                        <?php if ($history['note']): ?>
                                        <p class="mb-0 mt-1"><?php echo nl2br($history['note']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Information -->
                <div class="col-md-4">
                    <!-- Customer Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Thông tin khách hàng</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Họ tên:</strong><br><?php echo $order['customer_name']; ?></p>
                            <p><strong>Số điện thoại:</strong><br><?php echo $order['customer_phone']; ?></p>
                            <p><strong>Email:</strong><br><?php echo $order['customer_email']; ?></p>
                            <?php if ($order['user_id']): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/users" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-search"></i> Xem thông tin
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Ngày đặt:</strong><br><?php echo date('H:i d/m/Y', strtotime($order['created_at'])); ?></p>
                            <p><strong>Thời gian lấy:</strong><br><?php echo date('H:i d/m/Y', strtotime($order['pickup_time'])); ?></p>
                            
                            <?php if ($order['actual_pickup_time']): ?>
                            <p><strong>Thời gian lấy thực tế:</strong><br><?php echo date('H:i d/m/Y', strtotime($order['actual_pickup_time'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($order['pickup_note']): ?>
                            <p><strong>Ghi chú:</strong><br><?php echo nl2br($order['pickup_note']); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($order['note']): ?>
                            <p><strong>Ghi chú đơn hàng:</strong><br><?php echo nl2br($order['note']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <?php if ($payment): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-credit-card"></i> Thanh toán</h6>
                        </div>
                        <div class="card-body">
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
                            <p><strong>Phương thức:</strong><br><?php echo $payment_methods[$payment['method']]; ?></p>
                            <p><strong>Số tiền:</strong><br><?php echo number_format($payment['amount']); ?>₫</p>
                            <p><strong>Trạng thái:</strong><br>
                                <span class="badge bg-<?php echo $payment_status[$payment['status']]['class']; ?>">
                                    <?php echo $payment_status[$payment['status']]['text']; ?>
                                </span>
                            </p>
                            <?php if ($payment['paid_at']): ?>
                            <p><strong>Thời gian thanh toán:</strong><br><?php echo date('H:i d/m/Y', strtotime($payment['paid_at'])); ?></p>
                            <?php endif; ?>
                            <?php if ($payment['provider_reference']): ?>
                            <p><strong>Mã giao dịch:</strong><br><?php echo $payment['provider_reference']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Staff Info -->
                    <?php if ($order['staff_id']): ?>
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-user-tie"></i> Nhân viên xử lý</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Nhân viên phụ trách:</strong><br>ID: <?php echo $order['staff_id']; ?></p>
                            <p><strong>Cập nhật lúc:</strong><br><?php echo date('H:i d/m/Y', strtotime($order['updated_at'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
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

.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #0d6efd;
    border: 2px solid white;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #0d6efd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Status update form
    const statusForm = document.getElementById('statusForm');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderId = <?php echo $order['id']; ?>;
            
            fetch('<?php echo BASE_URL; ?>/admin/orders/update-status/' + orderId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật trạng thái thành công!');
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật trạng thái!');
            });
        });
    }
});
</script>