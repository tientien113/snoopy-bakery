<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <!-- Search Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>Kết quả tìm kiếm: "<?php echo htmlspecialchars($search_term); ?>"</h4>
                    <p class="text-muted">Tìm thấy <?php echo $result_count; ?> sản phẩm</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            <?php if (!empty($products)): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['discount_price']): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Giảm giá</span>
                        <?php endif; ?>
                        
                        <img src="<?php echo BASE_URL . '/public/' . ($product['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                             class="card-img-top" alt="<?php echo $product['name']; ?>" 
                             style="height: 200px; object-fit: cover; cursor: pointer;" 
                             onclick="window.location='<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>'">
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo $product['name']; ?></h6>
                            <p class="card-text text-muted small"><?php echo $product['category_name']; ?></p>
                            
                            <div class="price-section mt-auto">
                                <?php if ($product['discount_price']): ?>
                                    <span class="text-danger fw-bold"><?php echo number_format($product['discount_price']); ?>₫</span>
                                    <small class="text-muted text-decoration-line-through"><?php echo number_format($product['price']); ?>₫</small>
                                <?php else: ?>
                                    <span class="text-dark fw-bold"><?php echo number_format($product['price']); ?>₫</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-2">
                                <a href="<?php echo BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm w-100">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
                <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác.</p>
                <div class="mt-3">
                    <a href="<?php echo BASE_URL; ?>/products" class="btn btn-primary me-2">Xem tất cả sản phẩm</a>
                    <button onclick="history.back()" class="btn btn-outline-secondary">Quay lại</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>