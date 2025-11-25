<?php
/**
 * Product Controller - Quản lý sản phẩm
 */

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index() {
        $page = $_GET['page'] ?? 1;
        $category_id = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';

        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";

        $countSql = "SELECT COUNT(*) as total FROM products p WHERE p.is_active = 1";

        $params = [];

        if ($category_id) {
            $sql .= " AND p.category_id = :category_id";
            $countSql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        if ($search) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $countSql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        // Get total count
        $this->db->query($countSql);
        foreach ($params as $key => $value) {
            if ($key !== ':search') {
                $this->db->bind($key, $value);
            }
        }
        $totalResult = $this->db->result();
        $totalProducts = $totalResult['total'];
        $totalPages = ceil($totalProducts / $limit);

        // Get products
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $products = $this->db->results();

        $categories = $this->categoryModel->all();

        $data = [
            'title' => 'Sản phẩm - ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'current_category' => $category_id,
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_products' => $totalProducts
        ];

        $this->view('products/index', $data);
    }

    public function detail($id) {
        $product = $this->productModel->find($id);
        
        if (!$product || !$product['is_active']) {
            $this->setFlash('Sản phẩm không tồn tại', FLASH_ERROR);
            $this->redirect('products');
        }

        // Get related products
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            AND p.category_id = :category_id 
            AND p.id != :product_id 
            ORDER BY RAND() 
            LIMIT 4
        ");
        $this->db->bind(':category_id', $product['category_id']);
        $this->db->bind(':product_id', $id);
        $relatedProducts = $this->db->results();

        // Get reviews
        $this->db->query("
            SELECT r.*, u.name as user_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = :product_id 
            ORDER BY r.created_at DESC
        ");
        $this->db->bind(':product_id', $id);
        $reviews = $this->db->results();

        // Update view count
        $this->productModel->update($id, ['views' => $product['views'] + 1]);

        $data = [
            'title' => $product['name'] . ' - ' . APP_NAME,
            'product' => $product,
            'related_products' => $relatedProducts,
            'reviews' => $reviews
        ];

        $this->view('products/detail', $data);
    }

    public function search() {
        $search = $_GET['q'] ?? '';
        
        if (empty($search)) {
            $this->redirect('products');
        }

        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            AND (p.name LIKE :search OR p.description LIKE :search OR c.name LIKE :search)
            ORDER BY p.created_at DESC
        ");
        $this->db->bind(':search', "%$search%");
        $products = $this->db->results();

        $data = [
            'title' => 'Tìm kiếm: ' . $search . ' - ' . APP_NAME,
            'products' => $products,
            'search_term' => $search,
            'result_count' => count($products)
        ];

        $this->view('products/search', $data);
    }
}
?>