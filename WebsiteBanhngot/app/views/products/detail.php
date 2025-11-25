<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/products">Sản phẩm</a></li>
            <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-image mb-4">
                <img src="<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                     alt="<?php echo $product['name']; ?>" class="img-fluid rounded shadow">
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-6">
            <div class="product-info">
                <h1 class="product-title"><?php echo $product['name']; ?></h1>
                <p class="text-muted">Danh mục: <?php echo $product['category_name']; ?></p>
                
                <div class="price-section mb-3">
                    <?php if ($product['discount_price']): ?>
                        <span class="text-danger fw-bold h2"><?php echo number_format($product['discount_price']); ?>₫</span>
                        <span class="text-muted text-decoration-line-through h4 ms-2"><?php echo number_format($product['price']); ?>₫</span>
                        <span class="badge bg-danger ms-2">Tiết kiệm <?php echo number_format($product['price'] - $product['discount_price']); ?>₫</span>
                    <?php else: ?>
                        <span class="text-dark fw-bold h2"><?php echo number_format($product['price']); ?>₫</span>
                    <?php endif; ?>
                </div>
                
                <div class="stock-info mb-3">
                    <?php if ($product['quantity'] > 0): ?>
                        <span class="text-success"><i class="fas fa-check-circle"></i> Còn hàng</span>
                        <small class="text-muted ms-2">(<?php echo $product['quantity']; ?> sản phẩm)</small>
                    <?php else: ?>
                        <span class="text-danger"><i class="fas fa-times-circle"></i> Tạm hết hàng</span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['weight']): ?>
                <div class="product-weight mb-3">
                    <strong>Trọng lượng:</strong> <?php echo $product['weight']; ?>kg
                </div>
                <?php endif; ?>
                
                <?php if ($product['expiry_date']): ?>
                <div class="product-expiry mb-3">
                    <strong>Hạn sử dụng:</strong> <?php echo $product['expiry_date']; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($product['ingredients']): ?>
                <div class="product-ingredients mb-3">
                    <strong>Thành phần:</strong>
                    <p class="text-muted"><?php echo $product['ingredients']; ?></p>
                </div>
                <?php endif; ?>
                
                <div class="product-description mb-4">
                    <strong>Mô tả:</strong>
                    <p class="text-muted"><?php echo nl2br($product['description']); ?></p>
                </div>
                
                <!-- Add to Cart -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="add-to-cart-section">
                    <form id="addToCartForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label">Số lượng:</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width: 120px;">
                                    <button type="button" class="btn btn-outline-secondary quantity-minus">-</button>
                                    <input type="number" class="form-control text-center" id="quantity" name="quantity" 
                                           value="1" min="1" max="<?php echo $product['quantity']; ?>">
                                    <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-lg" 
                                        <?php echo $product['quantity'] == 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <a href="<?php echo BASE_URL; ?>/auth/login" class="alert-link">Đăng nhập</a> để mua hàng.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Product Description Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                            data-bs-target="#description" type="button" role="tab">
                        Mô tả chi tiết
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                            data-bs-target="#reviews" type="button" role="tab">
                        Đánh giá (<?php echo count($reviews); ?>)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content p-3 border border-top-0 rounded-bottom">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="product-full-description">
                        <?php echo nl2br($product['description']); ?>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <?php if (!empty($reviews)): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo $review['user_name']; ?></strong>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></p>
                                <p><?php echo nl2br($review['comment']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                <div class="col-md-3 col-6 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo BASE_URL . '/public/' . ($related['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                             class="card-img-top" alt="<?php echo $related['name']; ?>" 
                             style="height: 180px; object-fit: cover; cursor: pointer;" 
                             onclick="window.location='<?php echo BASE_URL; ?>/products/detail/<?php echo $related['id']; ?>'">
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo $related['name']; ?></h6>
                            
                            <div class="price-section mt-auto">
                                <?php if ($related['discount_price']): ?>
                                    <span class="text-danger fw-bold"><?php echo number_format($related['discount_price']); ?>₫</span>
                                    <small class="text-muted text-decoration-line-through"><?php echo number_format($related['price']); ?>₫</small>
                                <?php else: ?>
                                    <span class="text-dark fw-bold"><?php echo number_format($related['price']); ?>₫</span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="<?php echo BASE_URL; ?>/products/detail/<?php echo $related['id']; ?>" 
                               class="btn btn-outline-primary btn-sm mt-2">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    const quantityInput = document.getElementById('quantity');
    const minusBtn = document.querySelector('.quantity-minus');
    const plusBtn = document.querySelector('.quantity-plus');
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        let max = parseInt(quantityInput.max);
        if (value < max) {
            quantityInput.value = value + 1;
        }
    });
    
    // Add to cart form
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = <?php echo $product['id']; ?>;
            const quantity = quantityInput.value;
            
            fetch('<?php echo BASE_URL; ?>/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}&csrf_token=<?php echo $csrf_token; ?>`
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
    }
});
</script>