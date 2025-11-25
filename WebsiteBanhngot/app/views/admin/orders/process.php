<?php
require_once APP_PATH . '/views/layouts/header.php';
require_once APP_PATH . '/views/layouts/navbar.php';
require_once APP_PATH . '/views/layouts/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Xử lý đơn hàng #<?= $order['order_code'] ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/orders">Đơn hàng</a></li>
                        <li class="breadcrumb-item active">Xử lý đơn hàng</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Flash Messages -->
            <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= Session::getFlash('success') ?>
            </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= Session::getFlash('error') ?>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Order Information -->
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin đơn hàng</h3>
                            <div class="card-tools">
                                <span class="badge badge-<?= $orderStatusInfo['class'] ?>">
                                    <?= $orderStatusInfo['text'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Thông tin khách hàng</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Tên:</strong></td>
                                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Điện thoại:</strong></td>
                                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                        </tr>
                                        <?php if ($order['user_id']): ?>
                                        <tr>
                                            <td><strong>Tài khoản:</strong></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/users/edit/<?= $order['user_id'] ?>">
                                                    ID: <?= $order['user_id'] ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Thông tin đơn hàng</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Mã đơn:</strong></td>
                                            <td><?= $order['order_code'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày đặt:</strong></td>
                                            <td><?= FormatHelper::formatDateTime($order['created_at']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tổng tiền:</strong></td>
                                            <td class="text-success font-weight-bold"><?= FormatHelper::formatCurrency($order['total_price']) ?></td>
                                        </tr>
                                        <?php if ($order['discount_amount'] > 0): ?>
                                        <tr>
                                            <td><strong>Giảm giá:</strong></td>
                                            <td class="text-danger">-<?= FormatHelper::formatCurrency($order['discount_amount']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>Thành tiền:</strong></td>
                                            <td class="text-success font-weight-bold h5"><?= FormatHelper::formatCurrency($order['final_price']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <h5 class="mt-4">Chi tiết sản phẩm</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= BASE_URL ?>/uploads/products/<?= $item['image'] ?? 'default-product.jpg' ?>" 
                                                         alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                         class="img-thumbnail mr-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                        <?php if ($item['sku']): ?>
                                                        <br><small class="text-muted">SKU: <?= $item['sku'] ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right"><?= FormatHelper::formatCurrency($item['unit_price']) ?></td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-right text-success font-weight-bold">
                                                <?= FormatHelper::formatCurrency($item['unit_price'] * $item['quantity']) ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                                            <td class="text-right text-success font-weight-bold h5">
                                                <?= FormatHelper::formatCurrency($order['final_price']) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Customer Notes -->
                            <?php if ($order['note']): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Ghi chú của khách hàng:</strong><br>
                                <?= nl2br(htmlspecialchars($order['note'])) ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($order['pickup_note']): ?>
                            <div class="alert alert-warning mt-2">
                                <strong>Ghi chú lấy hàng:</strong><br>
                                <?= nl2br(htmlspecialchars($order['pickup_note'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Lịch sử trạng thái</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($statusHistory as $history): ?>
                                <div class="timeline-item">
                                    <div class="timeline-header">
                                        <strong><?= FormatHelper::formatDateTime($history['created_at']) ?></strong>
                                        <span class="badge badge-<?= FormatHelper::formatOrderStatus($history['new_status'])['class'] ?> ml-2">
                                            <?= FormatHelper::formatOrderStatus($history['new_status'])['text'] ?>
                                        </span>
                                    </div>
                                    <div class="timeline-body">
                                        <?php if ($history['changed_by_user_id']): ?>
                                        <small class="text-muted">
                                            Thay đổi bởi: User #<?= $history['changed_by_user_id'] ?>
                                        </small>
                                        <?php endif; ?>
                                        <?php if ($history['note']): ?>
                                        <p class="mb-0"><?= htmlspecialchars($history['note']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <!-- Status Update -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Cập nhật trạng thái</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= BASE_URL ?>/admin/orders/update-status" method="post">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="status">Trạng thái mới</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                        <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                        <option value="ready" <?= $order['status'] == 'ready' ? 'selected' : '' ?>>Sẵn sàng lấy</option>
                                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status_note">Ghi chú (tùy chọn)</label>
                                    <textarea class="form-control" id="status_note" name="status_note" rows="3" 
                                              placeholder="Lý do thay đổi trạng thái..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-sync-alt"></i> Cập nhật trạng thái
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin thanh toán</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($payment): ?>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Phương thức:</strong></td>
                                    <td><?= FormatHelper::formatPaymentMethod($payment['method']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Số tiền:</strong></td>
                                    <td class="text-success font-weight-bold"><?= FormatHelper::formatCurrency($payment['amount']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <span class="badge badge-<?= FormatHelper::formatPaymentStatus($payment['status'])['class'] ?>">
                                            <?= FormatHelper::formatPaymentStatus($payment['status'])['text'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($payment['paid_at']): ?>
                                <tr>
                                    <td><strong>Thanh toán lúc:</strong></td>
                                    <td><?= FormatHelper::formatDateTime($payment['paid_at']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($payment['provider_reference']): ?>
                                <tr>
                                    <td><strong>Mã tham chiếu:</strong></td>
                                    <td><code><?= $payment['provider_reference'] ?></code></td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <?php if ($payment['status'] == 'pending' && $payment['method'] != 'cash_on_pickup'): ?>
                            <button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#paymentModal">
                                <i class="fas fa-check"></i> Xác nhận thanh toán
                            </button>
                            <?php endif; ?>

                            <?php else: ?>
                            <p class="text-muted text-center">Chưa có thông tin thanh toán</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Hành động nhanh</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if ($order['status'] != 'cancelled' && $order['status'] != 'completed'): ?>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#cancelModal">
                                    <i class="fas fa-times"></i> Hủy đơn hàng
                                </button>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/admin/orders/detail/<?= $order['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-file-alt"></i> Xem chi tiết
                                </a>

                                <a href="javascript:window.print()" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i> In đơn hàng
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Assignment -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Phân công nhân viên</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= BASE_URL ?>/admin/orders/assign-staff" method="post">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="staff_id">Nhân viên phụ trách</label>
                                    <select class="form-control" id="staff_id" name="staff_id">
                                        <option value="">-- Chọn nhân viên --</option>
                                        <?php foreach ($staffMembers as $staff): ?>
                                        <option value="<?= $staff['id'] ?>" <?= $order['staff_id'] == $staff['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($staff['name']) ?> (<?= $staff['email'] ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-user-check"></i> Phân công
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Xác nhận đơn hàng #<?= $order['order_code'] ?> đã được thanh toán?</p>
                <form action="<?= BASE_URL ?>/admin/orders/confirm-payment" method="post" id="paymentForm">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="form-group">
                        <label for="reference">Mã tham chiếu (nếu có)</label>
                        <input type="text" class="form-control" id="reference" name="reference" 
                               placeholder="Mã giao dịch ngân hàng...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="submit" form="paymentForm" class="btn btn-success">Xác nhận thanh toán</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy đơn hàng #<?= $order['order_code'] ?>?</p>
                <form action="<?= BASE_URL ?>/admin/orders/cancel" method="post" id="cancelForm">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="form-group">
                        <label for="cancel_reason">Lý do hủy</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" 
                                  rows="3" required placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" form="cancelForm" class="btn btn-danger">Xác nhận hủy</button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 20px;
    border-left: 2px solid #007bff;
}
.timeline-item:before {
    content: '';
    position: absolute;
    left: -7px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
}
.timeline-header {
    margin-bottom: 5px;
}
.timeline-body {
    font-size: 0.9em;
}
</style>

<script>
$(document).ready(function() {
    // Auto-update status based on actions
    $('#status').change(function() {
        const newStatus = $(this).val();
        
        // Show/hide additional fields based on status
        if (newStatus === 'cancelled') {
            $('#status_note').attr('placeholder', 'Vui lòng nhập lý do hủy đơn hàng...');
        } else if (newStatus === 'completed') {
            $('#status_note').attr('placeholder', 'Ghi chú khi hoàn thành đơn hàng...');
        } else {
            $('#status_note').attr('placeholder', 'Ghi chú thay đổi trạng thái...');
        }
    });

    // Prevent form submission if cancelling without reason
    $('#cancelForm').submit(function(e) {
        const reason = $('#cancel_reason').val().trim();
        if (!reason) {
            e.preventDefault();
            alert('Vui lòng nhập lý do hủy đơn hàng');
            $('#cancel_reason').focus();
        }
    });
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>