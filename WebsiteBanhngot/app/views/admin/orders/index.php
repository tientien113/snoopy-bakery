<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-bag"></i> Quản lý Đơn hàng</h2>
                <span class="badge bg-primary"><?php echo $total_orders; ?> đơn hàng</span>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/orders">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="pending" <?php echo $current_status == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                        <option value="confirmed" <?php echo $current_status == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                        <option value="ready" <?php echo $current_status == 'ready' ? 'selected' : ''; ?>>Sẵn sàng</option>
                                        <option value="completed" <?php echo $current_status == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                        <option value="cancelled" <?php echo $current_status == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Mã đơn, tên khách hàng, số điện thoại...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Lọc
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Danh sách đơn hàng</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th width="150">Thao tác</th>
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
                                    <td>
                                        <?php echo $order['customer_name']; ?>
                                        <br><small class="text-muted"><?php echo $order['customer_phone']; ?></small>
                                    </td>
                                    <td><?php echo date('H:i d/m/Y', strtotime($order['created_at'])); ?></td>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo BASE_URL; ?>/admin/orders/detail/<?php echo $order['id']; ?>" 
                                               class="btn btn-outline-primary" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($order['status'] != 'completed' && $order['status'] != 'cancelled'): ?>
                                            <a href="<?php echo BASE_URL; ?>/admin/orders/detail/<?php echo $order['id']; ?>#status" 
                                               class="btn btn-outline-warning" title="Cập nhật trạng thái">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
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
                                   href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $current_page - 1; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    &laquo; Trước
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $i; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/orders?page=<?php echo $current_page + 1; ?><?php echo $current_status ? '&status=' . $current_status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
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
                            <?php if ($current_status || $search): ?>
                                Không tìm thấy đơn hàng nào
                            <?php else: ?>
                                Chưa có đơn hàng nào
                            <?php endif; ?>
                        </h5>
                        <?php if ($current_status || $search): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/orders" class="btn btn-primary mt-3">
                            <i class="fas fa-list"></i> Xem tất cả đơn hàng
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>