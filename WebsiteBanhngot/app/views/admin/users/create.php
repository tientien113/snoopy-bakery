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
                    <h1>Thêm người dùng mới</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/users">Người dùng</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin người dùng</h3>
                        </div>

                        <form action="<?= BASE_URL ?>/admin/users/create" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <!-- Flash Messages -->
                                <?php if (Session::hasFlash('error')): ?>
                                <div class="alert alert-danger">
                                    <?= Session::getFlash('error') ?>
                                </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                                   id="name" name="name" 
                                                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                                                   required>
                                            <?php if (isset($errors['name'])): ?>
                                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                                   id="email" name="email" 
                                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                                                   required>
                                            <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                                   id="password" name="password" required>
                                            <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                            <?php endif; ?>
                                            <small class="form-text text-muted">Mật khẩu ít nhất 6 ký tự</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                                   id="confirm_password" name="confirm_password" required>
                                            <?php if (isset($errors['confirm_password'])): ?>
                                            <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Số điện thoại</label>
                                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                                   id="phone" name="phone" 
                                                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                                            <?php if (isset($errors['phone'])): ?>
                                            <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role_id">Vai trò <span class="text-danger">*</span></label>
                                            <select class="form-control <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>" 
                                                    id="role_id" name="role_id" required>
                                                <option value="">Chọn vai trò</option>
                                                <?php foreach ($roles as $role): ?>
                                                <option value="<?= $role['id'] ?>" <?= ($old['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($role['name']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['role_id'])): ?>
                                            <div class="invalid-feedback"><?= $errors['role_id'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">Địa chỉ</label>
                                    <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                              id="address" name="address" 
                                              rows="3"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                                    <?php if (isset($errors['address'])): ?>
                                    <div class="invalid-feedback"><?= $errors['address'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                        <label class="custom-control-label" for="is_active">Kích hoạt tài khoản</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Thêm người dùng
                                </button>
                                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Hướng dẫn</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Vai trò người dùng:</strong></p>
                            <ul class="list-unstyled">
                                <li><span class="badge badge-primary">Admin</span> - Toàn quyền hệ thống</li>
                                <li><span class="badge badge-success">Quản lý</span> - Quản lý sản phẩm & đơn hàng</li>
                                <li><span class="badge badge-warning">Nhân viên</span> - Xử lý đơn hàng</li>
                                <li><span class="badge badge-secondary">Khách hàng</span> - Mua hàng</li>
                            </ul>
                            <hr>
                            <p class="text-muted small">
                                <i class="fas fa-info-circle"></i>
                                Mật khẩu sẽ được mã hóa trước khi lưu vào database.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Password strength check
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        updatePasswordStrength(strength);
    });

    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;
        
        return strength;
    }

    function updatePasswordStrength(strength) {
        const feedback = $('#password').siblings('.invalid-feedback');
        let message = '';
        let color = 'text-muted';
        
        switch(strength) {
            case 0:
            case 1:
                message = 'Mật khẩu rất yếu';
                color = 'text-danger';
                break;
            case 2:
                message = 'Mật khẩu yếu';
                color = 'text-warning';
                break;
            case 3:
                message = 'Mật khẩu trung bình';
                color = 'text-info';
                break;
            case 4:
                message = 'Mật khẩu mạnh';
                color = 'text-success';
                break;
            case 5:
                message = 'Mật khẩu rất mạnh';
                color = 'text-success';
                break;
        }
        
        if (message) {
            if (feedback.length) {
                feedback.text(message).removeClass().addClass('invalid-feedback ' + color);
            } else {
                $('#password').after('<div class="invalid-feedback ' + color + '">' + message + '</div>');
            }
        }
    }
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>