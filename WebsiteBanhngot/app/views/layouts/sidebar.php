<?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] != 4): ?>
<div class="col-md-3">
    <div class="list-group">
        <a href="<?php echo BASE_URL; ?>/admin" class="list-group-item list-group-item-action">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/products" class="list-group-item list-group-item-action">
            <i class="fas fa-cookie"></i> Quản lý Sản phẩm
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/categories" class="list-group-item list-group-item-action">
            <i class="fas fa-folder"></i> Quản lý Danh mục
        </a>
        
        <a href="<?php echo BASE_URL; ?>/admin/orders" class="list-group-item list-group-item-action">
            <i class="fas fa-shopping-bag"></i> Quản lý Đơn hàng
        </a>
        
        <?php if ($_SESSION['user_role'] == 1): ?>
        <a href="<?php echo BASE_URL; ?>/admin/users" class="list-group-item list-group-item-action">
            <i class="fas fa-users"></i> Quản lý Người dùng
        </a>
        <?php endif; ?>
        
        <a href="<?php echo BASE_URL; ?>/admin/reports" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-bar"></i> Báo cáo
        </a>
    </div>
</div>
<?php endif; ?>