<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Sửa sản phẩm</h2>
                <div>
                    <a href="<?php echo BASE_URL; ?>/admin/products" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin sản phẩm</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>/admin/products/edit/<?php echo $product['id']; ?>" 
                          enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm *</label>
                                    <input type="text" class="form-control" name="name" required 
                                           value="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Danh mục *</label>
                                            <select class="form-select" name="category_id" required>
                                                <option value="">Chọn danh mục</option>
                                                <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                    <?php echo $category['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" class="form-control" name="sku" 
                                                   value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>" 
                                                   placeholder="Mã sản phẩm">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá gốc *</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="price" required 
                                                       min="0" step="1000" value="<?php echo $product['price']; ?>">
                                                <span class="input-group-text">₫</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá khuyến mãi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="discount_price" 
                                                       min="0" step="1000" value="<?php echo $product['discount_price']; ?>">
                                                <span class="input-group-text">₫</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng *</label>
                                            <input type="number" class="form-control" name="quantity" required 
                                                   min="0" value="<?php echo $product['quantity']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Trọng lượng (kg)</label>
                                            <input type="number" class="form-control" name="weight" 
                                                   min="0" step="0.1" value="<?php echo $product['weight']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Hạn sử dụng</label>
                                            <input type="text" class="form-control" name="expiry_date" 
                                                   value="<?php echo htmlspecialchars($product['expiry_date'] ?? ''); ?>" 
                                                   placeholder="VD: 3 ngày">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Mô tả sản phẩm</label>
                                    <textarea class="form-control" name="description" rows="4" 
                                              placeholder="Mô tả chi tiết về sản phẩm..."><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Thành phần</label>
                                    <textarea class="form-control" name="ingredients" rows="3" 
                                              placeholder="Các thành phần chính..."><?php echo htmlspecialchars($product['ingredients'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Image Upload -->
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-image"></i> Hình ảnh</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <img id="imagePreview" 
                                                 src="<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                                                 class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                                        </div>
                                        <div class="mb-3">
                                            <input type="file" class="form-control" name="image" id="imageInput" 
                                                   accept="image/*">
                                            <small class="form-text text-muted">Chấp nhận: JPG, PNG, GIF, WebP (tối đa 5MB)</small>
                                            <?php if ($product['image']): ?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                                                <label class="form-check-label" for="removeImage">
                                                    Xóa ảnh hiện tại
                                                </label>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="card mt-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-cog"></i> Trạng thái</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" 
                                                   id="is_active" value="1" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">
                                                Hiển thị sản phẩm
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product Stats -->
                                <div class="card mt-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Thống kê</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="small">
                                            <p><strong>Lượt xem:</strong> <?php echo $product['views']; ?></p>
                                            <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y', strtotime($product['created_at'])); ?></p>
                                            <?php if ($product['updated_at']): ?>
                                            <p><strong>Cập nhật:</strong> <?php echo date('d/m/Y', strtotime($product['updated_at'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo"></i> Nhập lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Remove image checkbox
    const removeImageCheckbox = document.getElementById('removeImage');
    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                imagePreview.src = '<?php echo BASE_URL; ?>/public/assets/images/default-product.jpg';
            } else {
                imagePreview.src = '<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>';
            }
        });
    }
    
    // Price validation
    const priceInput = document.querySelector('input[name="price"]');
    const discountInput = document.querySelector('input[name="discount_price"]');
    
    function validatePrices() {
        const price = parseFloat(priceInput.value);
        const discount = parseFloat(discountInput.value);
        
        if (discount && discount >= price) {
            discountInput.setCustomValidity('Giá khuyến mãi phải nhỏ hơn giá gốc');
        } else {
            discountInput.setCustomValidity('');
        }
    }
    
    priceInput.addEventListener('input', validatePrices);
    discountInput.addEventListener('input', validatePrices);
});
</script>