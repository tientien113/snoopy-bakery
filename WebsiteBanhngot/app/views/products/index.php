<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>/products">
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
                        
                        <div class="mb-3">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Tên sản phẩm...">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </form>
                </div>
            </div>
            
            <!-- Categories List -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Danh mục</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>/products" 
                           class="list-group-item list-group-item-action <?php echo !$current_category ? 'active' : ''; ?>">
                            Tất cả sản phẩm
                        </a>
                        <?php foreach ($categories as $category): ?>
                        <a href="<?php echo BASE_URL; ?>/products?category=<?php echo $category['id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo ($current_category == $category['id']) ? 'active' : ''; ?>">
                            <?php echo $category['name']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <?php if ($search): ?>
                        Kết quả tìm kiếm: "<?php echo htmlspecialchars($search); ?>"
                    <?php elseif ($current_category): ?>
                        <?php 
                        $category_name = '';
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $current_category) {
                                $category_name = $cat['name'];
                                break;
                            }
                        }
                        ?>
                        Danh mục: <?php echo $category_name; ?>
                    <?php else: ?>
                        Tất cả sản phẩm
                    <?php endif; ?>
                </h4>
                <div class="text-muted">
                    Hiển thị <?php echo count($products); ?> trên tổng <?php echo $total_products; ?> sản phẩm
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['discount_price']): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Giảm giá</span>
                        <?php endif; ?>
                        
                        <?php if ($product['quantity'] == 0): ?>
                            <span class="badge bg-warning position-absolute top-0 end-0 m-2">Hết hàng</span>
                        <?php endif; ?>
                        
                        <img src="<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                             class="card-img-top" alt="<?php echo $product['name']; ?>" 
                             style="height: 250px; object-fit: cover; cursor: pointer;" 
                             onclick="window.location='<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>'">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $product['category_name']; ?></p>
                            
                            <p class="card-text small text-truncate"><?php echo $product['description']; ?></p>
                            
                            <div class="price-section mt-auto">
                                <?php if ($product['discount_price']): ?>
                                    <span class="text-danger fw-bold h5"><?php echo number_format($product['discount_price']); ?>₫</span>
                                    <span class="text-muted text-decoration-line-through small ms-2"><?php echo number_format($product['price']); ?>₫</span>
                                <?php else: ?>
                                    <span class="text-dark fw-bold h5"><?php echo number_format($product['price']); ?>₫</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="stock-info small text-muted">
                                <?php if ($product['quantity'] > 0): ?>
                                    Còn <?php echo $product['quantity']; ?> sản phẩm
                                <?php else: ?>
                                    <span class="text-danger">Tạm hết hàng</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-3 d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm flex-fill">Xem chi tiết</a>
                                
                                <?php if (isset($_SESSION['user_id']) && $product['quantity'] > 0): ?>
                                    <button class="btn btn-primary btn-sm add-to-cart" 
                                            data-product-id="<?php echo $product['id']; ?>"
                                            title="Thêm vào giỏ hàng">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="<?php echo BASE_URL; ?>/products?page=<?php echo $current_page - 1; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            &laquo; Trước
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" 
                           href="<?php echo BASE_URL; ?>/products?page=<?php echo $i; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="<?php echo BASE_URL; ?>/products?page=<?php echo $current_page + 1; ?><?php echo $current_category ? '&category=' . $current_category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                            Tiếp &raquo;
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
                <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác hoặc danh mục khác.</p>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary">Xem tất cả sản phẩm</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
            fetch('<?php echo BASE_URL; ?>/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1&csrf_token=<?php echo $csrf_token; ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Update cart count
                    const cartBadge = document.querySelector('.navbar .badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    } else {
                        // Create badge if not exists
                        const cartLink = document.querySelector('a[href*="/cart"]');
                        if (cartLink) {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-danger';
                            badge.textContent = data.cart_count;
                            cartLink.appendChild(badge);
                        }
                    }
                    
                    // Remove alert after 3 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.parentNode.removeChild(alertDiv);
                        }
                    }, 3000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm vào giỏ hàng!');
            });
        });
    });
});
</script>