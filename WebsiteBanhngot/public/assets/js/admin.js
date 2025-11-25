// Main JavaScript for SNOOPY Bakery Website

document.addEventListener('DOMContentLoaded', function() {
    initializeWebsite();
    setupEventListeners();
    handleFlashMessages();
});

// Initialize website functionality
function initializeWebsite() {
    console.log('SNOOPY Bakery Website initialized');
    
    // Set current year in footer
    const currentYear = new Date().getFullYear();
    const yearElements = document.querySelectorAll('.current-year');
    yearElements.forEach(element => {
        element.textContent = currentYear;
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Setup global event listeners
function setupEventListeners() {
    // Global click handler for dynamic content
    document.addEventListener('click', function(e) {
        // Handle external links
        if (e.target.matches('a[href^="http"]') && !e.target.href.includes(window.location.hostname)) {
            e.target.setAttribute('target', '_blank');
            e.target.setAttribute('rel', 'noopener noreferrer');
        }
    });
    
    // Handle form submissions with loading states
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                
                // Re-enable button after 10 seconds (safety net)
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Gửi';
                    }
                }, 10000);
            }
        });
    });
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Handle flash messages
function handleFlashMessages() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Auto-dismiss success alerts after 5 seconds
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
        
        // Add close button functionality
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }
    });
}

// Utility function to show notification
function showNotification(message, type = 'success') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    `;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }
    }, 5000);
}

// AJAX helper function
function makeRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'same-origin'
    };
    
    const config = { ...defaultOptions, ...options };
    
    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showNotification('Có lỗi xảy ra khi kết nối đến server', 'error');
            throw error;
        });
}

// Format price function
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Validate email function
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validate phone number function
function isValidPhone(phone) {
    const phoneRegex = /^(0|\+84)(\d{9,10})$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Image preview function
function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (input && preview) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    showNotification('Kích thước file quá lớn. Vui lòng chọn file nhỏ hơn 5MB.', 'error');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Quantity input controls
function setupQuantityControls(container) {
    const minusBtn = container.querySelector('.quantity-minus');
    const plusBtn = container.querySelector('.quantity-plus');
    const input = container.querySelector('.quantity-input');
    
    if (minusBtn && plusBtn && input) {
        minusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            if (value > parseInt(input.min)) {
                input.value = value - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        plusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            if (value < parseInt(input.max)) {
                input.value = value + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        input.addEventListener('change', () => {
            let value = parseInt(input.value);
            const min = parseInt(input.min);
            const max = parseInt(input.max);
            
            if (isNaN(value) || value < min) {
                input.value = min;
            } else if (value > max) {
                input.value = max;
            }
        });
    }
}

// Initialize all quantity controls on page
function initializeQuantityControls() {
    const quantityContainers = document.querySelectorAll('.input-group');
    quantityContainers.forEach(container => {
        if (container.querySelector('.quantity-input')) {
            setupQuantityControls(container);
        }
    });
}

// Cart functionality
class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    
    async addToCart(productId, quantity = 1) {
        try {
            const response = await makeRequest(`${BASE_URL}/cart/add`, {
                method: 'POST',
                body: `product_id=${productId}&quantity=${quantity}&csrf_token=${this.csrfToken}`
            });
            
            if (response.success) {
                this.updateCartCount(response.cart_count);
                showNotification(response.message, 'success');
                return true;
            } else {
                showNotification(response.message, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
            return false;
        }
    }
    
    updateCartCount(count) {
        const cartBadge = document.querySelector('.navbar .badge');
        const cartLink = document.querySelector('a[href*="/cart"]');
        
        if (cartBadge) {
            cartBadge.textContent = count;
        } else if (cartLink && count > 0) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-danger';
            badge.textContent = count;
            cartLink.appendChild(badge);
        }
        
        // Update any other cart count elements
        document.querySelectorAll('.cart-count').forEach(element => {
            element.textContent = count;
        });
    }
    
    async updateCartItem(cartId, quantity) {
        try {
            const response = await makeRequest(`${BASE_URL}/cart/update`, {
                method: 'POST',
                body: `cart_id=${cartId}&quantity=${quantity}&csrf_token=${this.csrfToken}`
            });
            
            if (response.success) {
                this.updateCartCount(response.cart_count);
                return true;
            } else {
                showNotification(response.message, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            showNotification('Có lỗi xảy ra khi cập nhật giỏ hàng', 'error');
            return false;
        }
    }
    
    async removeCartItem(cartId) {
        try {
            const response = await makeRequest(`${BASE_URL}/cart/remove/${cartId}`, {
                method: 'POST',
                body: `csrf_token=${this.csrfToken}`
            });
            
            if (response.success) {
                this.updateCartCount(response.cart_count);
                showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
                return true;
            } else {
                showNotification(response.message, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error removing cart item:', error);
            showNotification('Có lỗi xảy ra khi xóa sản phẩm', 'error');
            return false;
        }
    }
}

// Initialize cart manager
const cartManager = new CartManager();

// Export for global use
window.cartManager = cartManager;
window.showNotification = showNotification;
window.formatPrice = formatPrice;
window.isValidEmail = isValidEmail;
window.isValidPhone = isValidPhone;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeQuantityControls();
});