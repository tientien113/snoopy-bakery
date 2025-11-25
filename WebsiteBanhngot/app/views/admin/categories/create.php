<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plus-circle"></i> Thêm danh mục mới</h2>
                <a href="<?php echo BASE_URL; ?>/admin/categories" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>/admin/categories/create">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục *</label>
                            <input type="text" class="form-control" name="name" required 
                                   value="<?php echo $_POST['name'] ?? ''; ?>" 
                                   placeholder="VD: Bánh kem, Bánh mì, Bánh ngọt...">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="4" 
                                      placeholder="Mô tả về danh mục này..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo"></i> Nhập lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Help Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-question-circle"></i> Hướng dẫn</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p><strong>Tên danh mục:</strong></p>
                        <ul class="ps-3">
                            <li>Nên đặt tên ngắn gọn, dễ hiểu</li>
                            <li>Không trùng với danh mục khác</li>
                            <li>Phân loại rõ ràng sản phẩm</li>
                        </ul>
                        
                        <p><strong>Mô tả:</strong></p>
                        <ul class="ps-3">
                            <li>Mô tả ngắn về danh mục</li>
                            <li>Giúp khách hàng dễ tìm kiếm</li>
                            <li>Có thể để trống nếu không cần</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>