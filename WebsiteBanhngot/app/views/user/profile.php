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
                    <a href="<?php echo BASE_URL; ?>/user/profile" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i>Thông tin cá nhân
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/orders" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                    </a>
                    <a href="<?php echo BASE_URL; ?>/orders/history" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit"></i> Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>/user/profile">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên *</label>
                                    <input type="text" class="form-control" name="name" required 
                                           value="<?php echo htmlspecialchars($user['name']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                                    <small class="form-text text-muted">Email không thể thay đổi</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" 
                                           value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3"><i class="fas fa-lock"></i> Thay đổi mật khẩu</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" name="current_password">
                                    <small class="form-text text-muted">Chỉ điền nếu muốn đổi mật khẩu</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" name="confirm_password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật thông tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Info -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin tài khoản</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày tham gia:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $user['is_active'] ? 'Đang hoạt động' : 'Đã khóa'; ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Vai trò:</strong> 
                                <?php 
                                $roles = [
                                    1 => 'Quản trị viên',
                                    2 => 'Quản lý',
                                    3 => 'Nhân viên', 
                                    4 => 'Khách hàng'
                                ];
                                echo $roles[$user['role_id']];
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>