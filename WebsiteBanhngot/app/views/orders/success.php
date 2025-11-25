<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3 class="mb-0">Đặt hàng thành công!</h3>
                </div>
                <div class="card-body text-center">
                    <div class="success-icon mb-4">
                        <i class="fas fa-party-horn fa-4x text-success"></i>
                    </div>
                    
                    <h4 class="text-success mb-3">Cảm ơn bạn đã đặt hàng tại SNOOPY Bakery!</h4>
                    
                    <div class="order-info mb-4">
                        <p><strong>Mã đơn hàng:</strong> <span class="text-primary h5"><?php echo $order['order_code']; ?></span></p>
                        <p><strong>Tổng tiền:</strong> <span class="text-danger h5"><?php echo number_format($order['final_price']); ?>₫</span></p>
                        <p><strong>Thời gian lấy hàng:</strong> <?php echo date('H:i d/m/Y', strtotime($order['pickup_time'])); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo BRAND_ADDRESS; ?></p>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Hướng dẫn:</h6>
                        <ul class="text-start">
                            <li>Vui lòng đến cửa hàng đúng giờ đã đặt</li>
                            <li>Mang theo mã đơn hàng hoặc CMND/CCCD để nhận hàng</li>
                            <li>Liên hệ <?php echo BRAND_PHONE; ?> nếu có thay đổi</li>
                        </ul>
                    </div>
                    
                    <div class="action-buttons mt-4">
                        <a href="<?php echo BASE_URL; ?>/orders/detail/<?php echo $order['id']; ?>" 
                           class="btn btn-primary me-2">
                            <i class="fas fa-eye"></i> Xem chi tiết đơn hàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>/orders/history" class="btn btn-outline-primary me-2">
                            <i class="fas fa-history"></i> Lịch sử đơn hàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-success">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>