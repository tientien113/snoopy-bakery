<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>
            
            <?php if (!empty($cart_items)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th width="120">Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th width="80">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo BASE_URL . '/public/' . ($item['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                                                 alt="<?php echo $item['name']; ?>" 
                                                 class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                                <small class="text-muted">Tồn kho: <?php echo $item['stock_quantity']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($item['discount_price']): ?>
                                            <span class="text-danger fw-bold"><?php echo number_format($item['discount_price']); ?>₫</span>
                                            <br><small class="text-muted text-decoration-line-through"><?php echo number_format($item['price']); ?>₫</small>
                                        <?php else: ?>
                                            <span class="fw-bold"><?php echo number_format($item['price']); ?>₫</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary quantity-minus" 
                                                    data-cart-id="<?php echo $item['id']; ?>">-</button>
                                            <input type="number" class="form-control text-center quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['stock_quantity']; ?>"
                                                   data-cart-id="<?php echo $item['id']; ?>">
                                            <button type="button" class="btn btn-outline-secondary quantity-plus" 
                                                    data-cart-id="<?php echo $item['id']; ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        <?php 
                                        $item_price = $item['discount_price'] ?? $item['price'];
                                        $item_total = $item_price * $item['quantity'];
                                        echo number_format($item_total); ?>₫
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm remove-item" 
                                                data-cart-id="<?php echo $item['id']; ?>"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td colspan="2" class="fw-bold h5 text-danger"><?php echo number_format($total_price); ?>₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <a href="<?php echo BASE_URL; ?>/orders/checkout" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card"></i> Thanh toán
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Giỏ hàng trống</h4>
                <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để bắt đầu mua sắm.</p>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    
    // Quantity controls
    document.querySelectorAll('.quantity-minus').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
            let value = parseInt(input.value);
            if (value > 1) {
                updateCartQuantity(cartId, value - 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-plus').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
            let value = parseInt(input.value);
            let max = parseInt(input.max);
            if (value < max) {
                updateCartQuantity(cartId, value + 1);
            }
        });
    });
    
    // Input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.getAttribute('data-cart-id');
            let value = parseInt(this.value);
            let max = parseInt(this.max);
            let min = parseInt(this.min);
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
            updateCartQuantity(cartId, value);
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                removeCartItem(cartId);
            }
        });
    });
    
    function updateCartQuantity(cartId, quantity) {
        fetch('<?php echo BASE_URL; ?>/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${cartId}&quantity=${quantity}&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update totals
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra!');
        });
    }
    
    function removeCartItem(cartId) {
        fetch('<?php echo BASE_URL; ?>/cart/remove/' + cartId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra!');
        });
    }
});
</script>