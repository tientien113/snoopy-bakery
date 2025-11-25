<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Quản lý Người dùng</h2>
                <span class="badge bg-primary"><?php echo $total_users; ?> người dùng</span>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/users">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Vai trò</label>
                                    <select name="role" class="form-select">
                                        <option value="">Tất cả vai trò</option>
                                        <option value="1" <?php echo $current_role == '1' ? 'selected' : ''; ?>>Quản trị viên</option>
                                        <option value="2" <?php echo $current_role == '2' ? 'selected' : ''; ?>>Quản lý</option>
                                        <option value="3" <?php echo $current_role == '3' ? 'selected' : ''; ?>>Nhân viên</option>
                                        <option value="4" <?php echo $current_role == '4' ? 'selected' : ''; ?>>Khách hàng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Tên, email, số điện thoại...">
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

            <!-- Users Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Danh sách người dùng</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thông tin</th>
                                    <th>Vai trò</th>
                                    <th width="120">Trạng thái</th>
                                    <th width="150">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $user['name']; ?></strong>
                                        <br><small class="text-muted"><?php echo $user['email']; ?></small>
                                        <?php if ($user['phone']): ?>
                                            <br><small class="text-muted"><?php echo $user['phone']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $role_badges = [
                                            1 => ['class' => 'danger', 'text' => 'Quản trị viên'],
                                            2 => ['class' => 'warning', 'text' => 'Quản lý'],
                                            3 => ['class' => 'info', 'text' => 'Nhân viên'],
                                            4 => ['class' => 'success', 'text' => 'Khách hàng']
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $role_badges[$user['role_id']]['class']; ?>">
                                            <?php echo $role_badges[$user['role_id']]['text']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $user['is_active'] ? 'Hoạt động' : 'Đã khóa'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Role Update (Admin only) -->
                                            <?php if ($_SESSION['user_role'] == 1 && $user['id'] != $_SESSION['user_id']): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-warning dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown" title="Đổi vai trò">
                                                    <i class="fas fa-user-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php foreach ($role_badges as $role_id => $role): ?>
                                                        <?php if ($role_id != $user['role_id']): ?>
                                                        <li>
                                                            <a class="dropdown-item change-role" 
                                                               data-user-id="<?php echo $user['id']; ?>"
                                                               data-role-id="<?php echo $role_id; ?>"
                                                               data-role-name="<?php echo $role['text']; ?>">
                                                                <?php echo $role['text']; ?>
                                                            </a>
                                                        </li>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <!-- Status Toggle (Admin only) -->
                                            <?php if ($_SESSION['user_role'] == 1 && $user['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-<?php echo $user['is_active'] ? 'outline-danger' : 'outline-success'; ?> toggle-status"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-user-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                    data-current-status="<?php echo $user['is_active']; ?>"
                                                    title="<?php echo $user['is_active'] ? 'Khóa tài khoản' : 'Kích hoạt tài khoản'; ?>">
                                                <i class="fas fa-<?php echo $user['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                            </button>
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
                                   href="<?php echo BASE_URL; ?>/admin/users?page=<?php echo $current_page - 1; ?><?php echo $current_role ? '&role=' . $current_role : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    &laquo; Trước
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/users?page=<?php echo $i; ?><?php echo $current_role ? '&role=' . $current_role : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/users?page=<?php echo $current_page + 1; ?><?php echo $current_role ? '&role=' . $current_role : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    Tiếp &raquo;
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">
                            <?php if ($current_role || $search): ?>
                                Không tìm thấy người dùng nào
                            <?php else: ?>
                                Chưa có người dùng nào
                            <?php endif; ?>
                        </h5>
                        <?php if ($current_role || $search): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/users" class="btn btn-primary mt-3">
                            <i class="fas fa-list"></i> Xem tất cả người dùng
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Change role
    document.querySelectorAll('.change-role').forEach(link => {
        link.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const roleId = this.getAttribute('data-role-id');
            const roleName = this.getAttribute('data-role-name');
            const userName = this.closest('tr').querySelector('strong').textContent;
            
            if (confirm(`Bạn có chắc muốn đổi vai trò của "${userName}" thành "${roleName}"?`)) {
                fetch('<?php echo BASE_URL; ?>/admin/users/update-role/' + userId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `role_id=${roleId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thay đổi vai trò thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thay đổi vai trò!');
                });
            }
        });
    });
    
    // Toggle status
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const currentStatus = this.getAttribute('data-current-status');
            const action = currentStatus === '1' ? 'khóa' : 'kích hoạt';
            
            if (confirm(`Bạn có chắc muốn ${action} tài khoản của "${userName}"?`)) {
                fetch('<?php echo BASE_URL; ?>/admin/users/toggle-status/' + userId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Đã ${action} tài khoản thành công!`);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thay đổi trạng thái!');
                });
            }
        });
    });
});
</script>