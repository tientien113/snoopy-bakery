<?php
require_once APP_PATH . '/views/layouts/header.php';
require_once APP_PATH . '/views/layouts/navbar.php';
require_once APP_PATH . '/views/layouts/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Danh sách sản phẩm</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Flash Messages -->
            <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= Session::getFlash('success') ?>
            </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= Session::getFlash('error') ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Tất cả sản phẩm</h3>
                        <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="category-filter">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="status-filter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1">Đang bán</option>
                                <option value="0">Ngừng bán</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." id="search-input">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="80">Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Giá</th>
                                    <th>Giá KM</th>
                                    <th width="80">Tồn kho</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="100">Lượt xem</th>
                                    <th width="150">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <p>Không có sản phẩm nào</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <img src="<?= BASE_URL ?>/uploads/products/<?= $product['image'] ?? 'default-product.jpg' ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                        <?php if ($product['sku']): ?>
                                        <br><small class="text-muted">SKU: <?= $product['sku'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></td>
                                    <td class="text-right"><?= FormatHelper::formatCurrency($product['price']) ?></td>
                                    <td class="text-right">
                                        <?php if ($product['discount_price']): ?>
                                        <span class="text-danger"><?= FormatHelper::formatCurrency($product['discount_price']) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $product['quantity'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $product['quantity'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $product['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $product['is_active'] ? 'Đang bán' : 'Ngừng bán' ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= $product['views'] ?></td>
                                    <td><?= FormatHelper::formatDate($product['created_at']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product['id'] ?>" 
                                               class="btn btn-info" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/products/detail/<?= $product['id'] ?>" 
                                               target="_blank" 
                                               class="btn btn-secondary" 
                                               title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger delete-product" 
                                                    data-id="<?= $product['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pagination)): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            <?= $pagination->getShowingText() ?>
                        </div>
                        <?= $pagination->render() ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm "<strong id="product-name"></strong>"?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let deleteProductId = null;

    // Delete product confirmation
    $('.delete-product').click(function() {
        deleteProductId = $(this).data('id');
        $('#product-name').text($(this).data('name'));
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirm-delete').click(function() {
        if (deleteProductId) {
            window.location.href = '<?= BASE_URL ?>/admin/products/delete/' + deleteProductId;
        }
    });

    // Live search
    $('#search-btn').click(function() {
        performSearch();
    });

    $('#search-input').keypress(function(e) {
        if (e.which == 13) {
            performSearch();
        }
    });

    function performSearch() {
        const searchTerm = $('#search-input').val();
        const category = $('#category-filter').val();
        const status = $('#status-filter').val();
        
        let url = '<?= BASE_URL ?>/admin/products?';
        const params = [];
        
        if (searchTerm) params.push('search=' + encodeURIComponent(searchTerm));
        if (category) params.push('category=' + category);
        if (status) params.push('status=' + status);
        
        window.location.href = url + params.join('&');
    }

    // Apply filters on change
    $('#category-filter, #status-filter').change(function() {
        performSearch();
    });
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>