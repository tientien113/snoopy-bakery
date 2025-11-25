<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $BASE_URL; ?>">
                <i class="fas fa-birthday-cake"></i> SNOOPY BAKERY
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">SNOOPY BAKERY</h1>
            <p class="lead">Thiên đường của những chiếc bánh thơm ngon và đầy hấp dẫn</p>
            <a href="<?php echo $BASE_URL; ?>/products" class="btn btn-light btn-lg mt-3">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <div class="container my-5">
        <h2 class="text-center mb-5">Sản phẩm nổi bật</h2>
        
        <?php if (!empty($featured_products) && is_array($featured_products)): ?>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <img src="<?php echo $BASE_URL; ?>/assets/images/default-product.jpg" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text flex-grow-1">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        <div class="mt-auto">
                            <span class="h5 text-success">
                                <?php echo number_format($product['price'], 0, ',', '.'); ?> ₫
                            </span>
                            <a href="<?php echo $BASE_URL; ?>/products/detail/<?php echo $product['id']; ?>" 
                               class="btn btn-primary btn-sm w-100 mt-2">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-birthday-cake fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Chưa có sản phẩm nào</h4>
            <p class="text-muted">Sản phẩm sẽ được cập nhật sớm</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Features Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shipping-fast fa-2x text-primary"></i>
                    </div>
                    <h5>Giao hàng nhanh</h5>
                    <p class="text-muted">Nhận hàng trong vòng 2 giờ</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-award fa-2x text-primary"></i>
                    </div>
                    <h5>Chất lượng tốt</h5>
                    <p class="text-muted">Nguyên liệu tươi ngon nhất</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-2x text-primary"></i>
                    </div>
                    <h5>Hỗ trợ 24/7</h5>
                    <p class="text-muted">Đội ngũ tư vấn nhiệt tình</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-birthday-cake"></i> SNOOPY BAKERY</h5>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; Website Bánh Ngọt. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>