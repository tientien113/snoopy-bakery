<?php include '../app/views/layouts/header.php'; ?>
<?php include '../app/views/layouts/navbar.php'; ?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../app/views/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar"></i> Báo cáo & Thống kê</h2>
                <span class="text-muted"><?php echo date('d/m/Y'); ?></span>
            </div>

            <!-- Date Filters -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc thời gian</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/reports">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Khoảng thời gian</label>
                                    <select name="period" class="form-select" onchange="this.form.submit()">
                                        <option value="today" <?php echo $period == 'today' ? 'selected' : ''; ?>>Hôm nay</option>
                                        <option value="week" <?php echo $period == 'week' ? 'selected' : ''; ?>>7 ngày qua</option>
                                        <option value="month" <?php echo $period == 'month' ? 'selected' : ''; ?>>30 ngày qua</option>
                                        <option value="year" <?php echo $period == 'year' ? 'selected' : ''; ?>>1 năm qua</option>
                                        <option value="custom" <?php echo $period == 'custom' ? 'selected' : ''; ?>>Tùy chọn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Từ ngày</label>
                                    <input type="date" class="form-control" name="start_date" 
                                           value="<?php echo $start_date; ?>" 
                                           <?php echo $period != 'custom' ? 'disabled' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Đến ngày</label>
                                    <input type="date" class="form-control" name="end_date" 
                                           value="<?php echo $end_date; ?>"
                                           <?php echo $period != 'custom' ? 'disabled' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Doanh thu
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($reports['summary']['revenue'] ?? 0); ?>₫
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Đơn hàng
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($reports['summary']['orders'] ?? 0); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Đơn hàng TB
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $avg_order = ($reports['summary']['revenue'] ?? 0) / max(($reports['summary']['orders'] ?? 1), 1);
                                        echo number_format($avg_order); ?>₫
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ngày có doanh thu
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($reports['daily'] ?? []); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Daily Revenue Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-line"></i> Doanh thu theo ngày</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($reports['daily'])): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Doanh thu</th>
                                            <th>Số đơn</th>
                                            <th>Đơn hàng TB</th>
                                            <th>Biểu đồ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reports['daily'] as $daily): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($daily['date'])); ?></td>
                                            <td class="text-danger fw-bold"><?php echo number_format($daily['revenue']); ?>₫</td>
                                            <td><?php echo $daily['orders']; ?></td>
                                            <td><?php echo number_format($daily['revenue'] / max($daily['orders'], 1)); ?>₫</td>
                                            <td>
                                                <?php 
                                                $max_revenue = max(array_column($reports['daily'], 'revenue'));
                                                $width = $max_revenue > 0 ? ($daily['revenue'] / $max_revenue) * 100 : 0;
                                                ?>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?php echo $width; ?>%" 
                                                         aria-valuenow="<?php echo $width; ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?php if ($width > 30): ?>
                                                            <?php echo number_format($daily['revenue']); ?>₫
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center py-5">Không có dữ liệu doanh thu trong khoảng thời gian này</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Top Products -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-trophy"></i> Sản phẩm bán chạy</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($top_products)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($top_products as $index => $product): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php if ($index < 3): ?>
                                                <span class="badge bg-warning me-2"><?php echo $index + 1; ?></span>
                                            <?php endif; ?>
                                            <?php echo $product['name']; ?>
                                        </h6>
                                        <small class="text-muted">Đã bán: <?php echo $product['total_sold']; ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        <?php echo number_format($product['total_revenue']); ?>₫
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center py-3">Không có dữ liệu sản phẩm bán chạy</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card shadow mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-pie"></i> Thống kê nhanh</h6>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <p><strong>Khoảng thời gian:</strong><br>
                                    <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?>
                                </p>
                                <p><strong>Tổng doanh thu:</strong><br>
                                    <span class="text-danger fw-bold"><?php echo number_format($reports['summary']['revenue'] ?? 0); ?>₫</span>
                                </p>
                                <p><strong>Tổng đơn hàng:</strong><br>
                                    <?php echo number_format($reports['summary']['orders'] ?? 0); ?> đơn
                                </p>
                                <p><strong>Đơn hàng trung bình:</strong><br>
                                    <?php 
                                    $avg = ($reports['summary']['revenue'] ?? 0) / max(($reports['summary']['orders'] ?? 1), 1);
                                    echo number_format($avg); ?>₫
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df!important; }
.border-left-success { border-left: 0.25rem solid #1cc88a!important; }
.border-left-info { border-left: 0.25rem solid #36b9cc!important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e!important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable/disable date inputs based on period selection
    const periodSelect = document.querySelector('select[name="period"]');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    function toggleDateInputs() {
        const isCustom = periodSelect.value === 'custom';
        startDateInput.disabled = !isCustom;
        endDateInput.disabled = !isCustom;
    }
    
    periodSelect.addEventListener('change', toggleDateInputs);
    toggleDateInputs(); // Initial call
});
</script>