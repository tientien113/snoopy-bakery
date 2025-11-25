<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-folder"></i> Quản lý Danh mục</h2>
                <a href="<?php echo BASE_URL; ?>/admin/categories/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Danh sách danh mục</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Mô tả</th>
                                    <th width="150">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td>
                                        <strong><?php echo $category['name']; ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($category['description']): ?>
                                            <?php echo mb_strlen($category['description']) > 100 ? 
                                                mb_substr($category['description'], 0, 100) . '...' : 
                                                $category['description']; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Không có mô tả</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo BASE_URL; ?>/admin/categories/edit/<?php echo $category['id']; ?>" 
                                               class="btn btn-outline-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger delete-category" 
                                                    data-category-id="<?php echo $category['id']; ?>"
                                                    data-category-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có danh mục nào</h5>
                        <p class="text-muted">Hãy thêm danh mục đầu tiên của bạn.</p>
                        <a href="<?php echo BASE_URL; ?>/admin/categories/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm danh mục
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
    
    // Delete category buttons
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            
            if (confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?`)) {
                fetch('<?php echo BASE_URL; ?>/admin/categories/delete/' + categoryId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${csrfToken}`
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        alert('Đã xóa danh mục thành công!');
                        location.reload();
                    } else if (data) {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa danh mục!');
                });
            }
        });
    });
});
</script>