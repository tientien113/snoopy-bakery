<?php
/**
 * Category Controller - Quản lý danh mục (Admin only)
 */

class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
        $this->requireStaff(); // Only staff and above can access
    }

    public function index() {
        $categories = $this->categoryModel->all();

        $data = [
            'title' => 'Quản lý Danh mục - ' . APP_NAME,
            'categories' => $categories,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/categories/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        }

        $data = [
            'title' => 'Thêm Danh mục - ' . APP_NAME,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/categories/create', $data);
    }

    private function handleCreate() {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $this->setFlash('Vui lòng nhập tên danh mục', FLASH_ERROR);
            return;
        }

        // Check if category name exists
        $existing = $this->categoryModel->where(['name' => $name]);
        if (!empty($existing)) {
            $this->setFlash('Tên danh mục đã tồn tại', FLASH_ERROR);
            return;
        }

        $categoryData = [
            'name' => $name,
            'description' => $description
        ];

        if ($this->categoryModel->create($categoryData)) {
            $this->setFlash('Thêm danh mục thành công!', FLASH_SUCCESS);
            $this->redirect('admin/categories');
        } else {
            $this->setFlash('Có lỗi xảy ra khi thêm danh mục', FLASH_ERROR);
        }
    }

    public function edit($id) {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->setFlash('Danh mục không tồn tại', FLASH_ERROR);
            $this->redirect('admin/categories');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id);
            $category = $this->categoryModel->find($id); // Refresh data
        }

        $data = [
            'title' => 'Sửa Danh mục - ' . APP_NAME,
            'category' => $category,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('admin/categories/edit', $data);
    }

    private function handleEdit($id) {
        $this->validateCSRF();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $this->setFlash('Vui lòng nhập tên danh mục', FLASH_ERROR);
            return;
        }

        // Check if category name exists (excluding current category)
        $this->db->query("SELECT id FROM categories WHERE name = :name AND id != :id");
        $this->db->bind(':name', $name);
        $this->db->bind(':id', $id);
        $existing = $this->db->result();

        if ($existing) {
            $this->setFlash('Tên danh mục đã tồn tại', FLASH_ERROR);
            return;
        }

        $categoryData = [
            'name' => $name,
            'description' => $description
        ];

        if ($this->categoryModel->update($id, $categoryData)) {
            $this->setFlash('Cập nhật danh mục thành công!', FLASH_SUCCESS);
        } else {
            $this->setFlash('Có lỗi xảy ra khi cập nhật danh mục', FLASH_ERROR);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], HTTP_METHOD_NOT_ALLOWED);
        }

        $this->validateCSRF();

        // Check if category has products
        $this->db->query("SELECT COUNT(*) as count FROM products WHERE category_id = :category_id");
        $this->db->bind(':category_id', $id);
        $result = $this->db->result();

        if ($result['count'] > 0) {
            $this->setFlash('Không thể xóa danh mục đang có sản phẩm', FLASH_ERROR);
            $this->redirect('admin/categories');
        }

        if ($this->categoryModel->hardDelete($id)) {
            $this->setFlash('Xóa danh mục thành công!', FLASH_SUCCESS);
        } else {
            $this->setFlash('Có lỗi xảy ra khi xóa danh mục', FLASH_ERROR);
        }

        $this->redirect('admin/categories');
    }
}
?>