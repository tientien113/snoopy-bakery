<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-bag"></i> Thông tin đơn hàng</h4>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="row align-items-center mb-3 pb-3 border-bottom">
                        <div class="col-2">
                            <img src="<?php echo BASE_URL . '/public/' . ($item['image'] ?: 'assets/images/default-product.jpg'); ?>" 
                                 alt="<?php echo $item['name']; ?>" 
                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                        <div class="col-5">
                            <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                            <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                        </div>
                        <div class="col-3 text-end">
                            <?php 
                            $item_price = $item['discount_price'] ?? $item['price'];
                            echo number_format($item_price); ?>₫
                        </div>
                        <div class="col-2 text-end fw-bold">
                            <?php echo number_format($item_price * $item['quantity']); ?>₫
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="row mt-3">
                        <div class="col-8 text-end"><strong>Tạm tính:</strong></div>
                        <div class="col-4 text-end"><?php echo number_format($total_price); ?>₫</div>
                        
                        <div class="col-8 text-end"><strong>Giảm giá:</strong></div>
                        <div class="col-4 text-end text-danger">-0₫</div>
                        
                        <div class="col-8 text-end"><strong>Tổng cộng:</strong></div>
                        <div class="col-4 text-end fw-bold h5 text-danger"><?php echo number_format($total_price); ?>₫</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-truck"></i> Thông tin nhận hàng</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>/orders/checkout">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Họ tên *</label>
                            <input type="text" class="form-control" name="customer_name" required 
                                   value="<?php echo $user['name']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control" name="customer_phone" required 
                                   value="<?php echo $user['phone']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Thời gian lấy hàng *</label>
                            <input type="datetime-local" class="form-control" name="pickup_time" required 
                                   min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi chú cho cửa hàng</label>
                            <textarea class="form-control" name="pickup_note" rows="3" 
                                      placeholder="Ví dụ: Gọi điện trước 30 phút..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select class="form-select" name="payment_method">
                                <option value="cash_on_pickup">Thanh toán khi nhận hàng</option>
                                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                <option value="momo">Ví MoMo</option>
                                <option value="vnpay">VNPay</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Đặt hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> Lưu ý:</h6>
                    <ul class="small text-muted">
                        <li>Quý khách vui lòng đến đúng giờ đã hẹn</li>
                        <li>Có thể thay đổi thời gian lấy hàng trước 2 tiếng</li>
                        <li>Liên hệ <?php echo BRAND_PHONE; ?> để được hỗ trợ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>