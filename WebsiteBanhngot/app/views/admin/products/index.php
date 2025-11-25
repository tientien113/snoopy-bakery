<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-cookie"></i> Quản lý Sản phẩm</h2>
                <a href="<?php echo BASE_URL; ?>/admin/products/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm sản phẩm
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/products">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category" class="form-select">
                                        <option value="">Tất cả danh mục</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                            <?php echo ($current_category == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Tên sản phẩm...">
                                </div>
                            </div>
                            <div class="col-md-4">
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

            <!-- Products Table -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Danh sách sản phẩm</h6>
                    <span class="badge bg-light text-dark"><?php echo $total_products; ?> sản phẩm</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th width="120">Giá</th>
                                    <th width="100">Tồn kho</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="150">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                                             alt="<?php echo $product['name']; ?>" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong><?php echo $product['name']; ?></strong>
                                        <?php if ($product['sku']): ?>
                                            <br><small class="text-muted">SKU: <?php echo $product['sku']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['category_name']; ?></td>
                                    <td>
                                        <?php if ($product['discount_price']): ?>
                                            <span class="text-danger fw-bold"><?php echo number_format($product['discount_price']); ?>₫</span>
                                            <br><small class="text-muted text-decoration-line-through"><?php echo number_format($product['price']); ?>₫</small>
                                        <?php else: ?>
                                            <span class="fw-bold"><?php echo number_format($product['price']); ?>₫</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['quantity'] > 10 ? 'success' : ($product['quantity'] > 0 ? 'warning' : 'danger'); ?>">
                                            <?php echo $product['quantity']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $product['is_active'] ? 'Hiển thị' : 'Ẩn'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-primary" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/admin/products/edit/<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger delete-product" 
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
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

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/products?page=<?php echo $current_page - 1; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    &laquo; Trước
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/products?page=<?php echo $i; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?php echo BASE_URL; ?>/admin/products?page=<?php echo $current_page + 1; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    Tiếp &raquo;
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy sản phẩm nào</h5>
                        <p class="text-muted">Hãy thêm sản phẩm đầu tiên của bạn.</p>
                        <a href="<?php echo BASE_URL; ?>/admin/products/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
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
    
    // Delete product buttons
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            if (confirm(`Bạn có chắc muốn xóa sản phẩm "${productName}"?`)) {
                fetch('<?php echo BASE_URL; ?>/admin/products/delete/' + productId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã xóa sản phẩm thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm!');
                });
            }
        });
    });
});
</script>