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
                    <h1>Sửa thông tin người dùng</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/users">Người dùng</a></li>
                        <li class="breadcrumb-item active">Sửa thông tin</li>
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

                        <form action="<?= BASE_URL ?>/admin/users/edit/<?= $user['id'] ?>" method="post">
                            <div class="card-body">
                                <!-- Flash Messages -->
                                <?php if (Session::hasFlash('success')): ?>
                                <div class="alert alert-success">
                                    <?= Session::getFlash('success') ?>
                                </div>
                                <?php endif; ?>

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
                                                   value="<?= htmlspecialchars($old['name'] ?? $user['name']) ?>" 
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
                                                   value="<?= htmlspecialchars($old['email'] ?? $user['email']) ?>" 
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
                                            <label for="password">Mật khẩu mới</label>
                                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                                   id="password" name="password">
                                            <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                            <?php endif; ?>
                                            <small class="form-text text-muted">Để trống nếu không đổi mật khẩu</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                                   id="confirm_password" name="confirm_password">
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
                                                   value="<?= htmlspecialchars($old['phone'] ?? $user['phone']) ?>">
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
                                                <option value="<?= $role['id'] ?>" <?= ($old['role_id'] ?? $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
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
                                              rows="3"><?= htmlspecialchars($old['address'] ?? $user['address']) ?></textarea>
                                    <?php if (isset($errors['address'])): ?>
                                    <div class="invalid-feedback"><?= $errors['address'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                               <?= ($old['is_active'] ?? $user['is_active']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="is_active">Kích hoạt tài khoản</label>
                                    </div>
                                </div>

                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Thông tin hệ thống</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Ngày tạo:</dt>
                                            <dd class="col-sm-8"><?= FormatHelper::formatDateTime($user['created_at']) ?></dd>
                                            
                                            <dt class="col-sm-4">Cập nhật cuối:</dt>
                                            <dd class="col-sm-8"><?= $user['updated_at'] ? FormatHelper::formatDateTime($user['updated_at']) : 'Chưa cập nhật' ?></dd>
                                            
                                            <dt class="col-sm-4">ID người dùng:</dt>
                                            <dd class="col-sm-8"><?= $user['id'] ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                
                                <?php if ($user['id'] != AuthHelper::getUserId()): ?>
                                <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteModal">
                                    <i class="fas fa-trash"></i> Xóa người dùng
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Lưu ý quan trọng</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Chú ý:</strong> Không thể chỉnh sửa thông tin của chính mình.
                            </div>
                            
                            <p><strong>Về mật khẩu:</strong></p>
                            <ul class="small">
                                <li>Chỉ điền mật khẩu nếu muốn thay đổi</li>
                                <li>Mật khẩu mới phải có ít nhất 6 ký tự</li>
                                <li>Mật khẩu sẽ được mã hóa tự động</li>
                            </ul>
                            
                            <p><strong>Về vai trò:</strong></p>
                            <ul class="small">
                                <li>Chỉ Admin có thể phân quyền Admin</li>
                                <li>Không thể hạ cấp chính mình</li>
                                <li>Kiểm tra kỹ trước khi thay đổi vai trò</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<?php if ($user['id'] != AuthHelper::getUserId()): ?>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa người dùng "<strong><?= htmlspecialchars($user['name']) ?></strong>"?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cảnh báo: Hành động này không thể hoàn tác và sẽ xóa tất cả dữ liệu liên quan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <a href="<?= BASE_URL ?>/admin/users/delete/<?= $user['id'] ?>" class="btn btn-danger">Xóa người dùng</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Prevent self-role change
    const currentUserId = <?= AuthHelper::getUserId() ?>;
    const editingUserId = <?= $user['id'] ?>;
    
    if (currentUserId === editingUserId) {
        $('#role_id').prop('disabled', true).after('<small class="form-text text-warning">Bạn không thể thay đổi vai trò của chính mình</small>');
    }

    // Password confirmation check
    $('#confirm_password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword && confirmPassword !== '') {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Mật khẩu xác nhận không khớp');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>