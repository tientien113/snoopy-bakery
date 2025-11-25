<?php
require_once APP_PATH . '/views/layouts/header.php';
require_once APP_PATH . '/views/layouts/navbar.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Danh mục</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item <?= empty($currentCategory) ? 'active' : '' ?>">
                            <a href="<?= BASE_URL ?>/products" class="text-decoration-none">Tất cả sản phẩm</a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                        <li class="list-group-item <?= $currentCategory == $category['id'] ? 'active' : '' ?>">
                            <a href="<?= BASE_URL ?>/products/category/<?= $category['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($category['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Filter by Price -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lọc theo giá</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= BASE_URL ?>/products">
                        <div class="mb-3">
                            <label class="form-label">Giá từ</label>
                            <input type="number" name="min_price" class="form-control" value="<?= $minPrice ?? '' ?>" placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đến</label>
                            <input type="number" name="max_price" class="form-control" value="<?= $maxPrice ?? '' ?>" placeholder="1000000">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Lọc</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?= $pageTitle ?></h2>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                        <option value="<?= BASE_URL ?>/products?sort=name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="<?= BASE_URL ?>/products?sort=name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                        <option value="<?= BASE_URL ?>/products?sort=price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                        <option value="<?= BASE_URL ?>/products?sort=price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                        <option value="<?= BASE_URL ?>/products?sort=newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
                <p class="text-muted">Hãy thử tìm kiếm với điều kiện khác</p>
                <a href="<?= BASE_URL ?>/products" class="btn btn-primary">Xem tất cả sản phẩm</a>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 product-card">
                        <?php if ($product['discount_price']): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">-<?= FormatHelper::calculateDiscountPercent($product['price'], $product['discount_price']) ?>%</span>
                        <?php endif; ?>
                        
                        <img src="<?= BASE_URL ?>/uploads/products/<?= $product['image'] ?? 'default-product.jpg' ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= FormatHelper::truncateText($product['description'] ?? '', 80) ?>
                            </p>
                            
                            <div class="price-section mb-2">
                                <?php if ($product['discount_price']): ?>
                                <span class="text-danger fw-bold h5"><?= FormatHelper::formatCurrency($product['discount_price']) ?></span>
                                <span class="text-muted text-decoration-line-through small ms-2"><?= FormatHelper::formatCurrency($product['price']) ?></span>
                                <?php else: ?>
                                <span class="text-dark fw-bold h5"><?= FormatHelper::formatCurrency($product['price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="stock-info mb-2">
                                <small class="<?= $product['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $product['quantity'] > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                                </small>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>/products/detail/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                                <?php if ($product['quantity'] > 0): ?>
                                <button class="btn btn-primary btn-sm add-to-cart" data-product-id="<?= $product['id'] ?>">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($pagination)): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    <?= $pagination->getShowingText() ?>
                </div>
                <?= $pagination->render() ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.add-to-cart').click(function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        $.post('<?= BASE_URL ?>/cart/add', { product_id: productId, quantity: 1 })
            .done(function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    updateCartCount();
                } else {
                    showToast('error', response.message);
                }
            })
            .fail(function() {
                showToast('error', 'Lỗi kết nối');
            });
    });

    function updateCartCount() {
        $.get('<?= BASE_URL ?>/cart/count')
            .done(function(count) {
                $('#cart-count').text(count);
            });
    }
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>