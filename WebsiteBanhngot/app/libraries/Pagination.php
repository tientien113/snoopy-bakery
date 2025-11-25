<?php
class Pagination {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $urlPattern;
    
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1, $urlPattern = '?page={page}') {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = ceil($totalItems / $itemsPerPage);
        $this->urlPattern = $urlPattern;
    }
    
    // Get LIMIT and OFFSET for SQL query
    public function getSqlLimit() {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;
        return "LIMIT {$this->itemsPerPage} OFFSET {$offset}";
    }
    
    // Generate pagination HTML
    public function render($showNumbers = true) {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        // Previous button
        if ($this->currentPage > 1) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $this->getPageUrl($this->currentPage - 1) . '" aria-label="Previous">';
            $html .= '<span aria-hidden="true">&laquo;</span>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link" aria-hidden="true">&laquo;</span>';
            $html .= '</li>';
        }
        
        // Page numbers
        if ($showNumbers) {
            $startPage = max(1, $this->currentPage - 2);
            $endPage = min($this->totalPages, $this->currentPage + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $this->currentPage) {
                    $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPageUrl($i) . '">' . $i . '</a></li>';
                }
            }
        }
        
        // Next button
        if ($this->currentPage < $this->totalPages) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $this->getPageUrl($this->currentPage + 1) . '" aria-label="Next">';
            $html .= '<span aria-hidden="true">&raquo;</span>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link" aria-hidden="true">&raquo;</span>';
            $html .= '</li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    // Get URL for specific page
    private function getPageUrl($page) {
        return str_replace('{page}', $page, $this->urlPattern);
    }
    
    // Getters
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    // Get showing text (e.g., "Showing 1-10 of 100 items")
    public function getShowingText() {
        $start = (($this->currentPage - 1) * $this->itemsPerPage) + 1;
        $end = min($this->currentPage * $this->itemsPerPage, $this->totalItems);
        
        return "Hiển thị {$start}-{$end} của {$this->totalItems} mục";
    }
}
?>