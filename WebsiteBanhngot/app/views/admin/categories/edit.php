<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Sửa danh mục</h2>
                <a href="<?php echo BASE_URL; ?>/admin/categories" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>/admin/categories/edit/<?php echo $category['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục *</label>
                            <input type="text" class="form-control" name="name" required 
                                   value="<?php echo htmlspecialchars($category['name']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo"></i> Nhập lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Category Info -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p><strong>ID:</strong> <?php echo $category['id']; ?></p>
                        <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></p>
                        
                        <?php
                        // Get product count for this category
                        $product_count = 0;
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $category['id']) {
                                $product_count = $cat['product_count'] ?? 0;
                                break;
                            }
                        }
                        ?>
                        <p><strong>Số sản phẩm:</strong> 
                            <span class="badge bg-<?php echo $product_count > 0 ? 'success' : 'secondary'; ?>">
                                <?php echo $product_count; ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="card mt-3 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Vùng nguy hiểm</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        <i class="fas fa-info-circle"></i> 
                        Chỉ xóa danh mục khi không còn sản phẩm nào thuộc danh mục này.
                    </p>
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 delete-category"
                            data-category-id="<?php echo $category['id']; ?>"
                            data-category-name="<?php echo htmlspecialchars($category['name']); ?>">
                        <i class="fas fa-trash"></i> Xóa danh mục
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Delete category button
    const deleteBtn = document.querySelector('.delete-category');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            
            if (confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?\n\nHành động này không thể hoàn tả!`)) {
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
                        window.location.href = '<?php echo BASE_URL; ?>/admin/categories';
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
    }
});
</script>