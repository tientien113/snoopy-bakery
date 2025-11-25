// Main JavaScript file for bakery website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Cart count update
    updateCartCount();

    // Toast notification function
    window.showToast = function(type, message) {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        toastEl.setAttribute('role', 'alert');
        
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        // Remove toast after hide
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    // Update cart count from server
    async function updateCartCount() {
        try {
            const response = await fetch(`${BASE_URL}/api/cart/count`);
            if (response.ok) {
                const count = await response.text();
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.textContent = count;
                    if (count > 0) {
                        cartBadge.classList.add('badge-pulse');
                    } else {
                        cartBadge.classList.remove('badge-pulse');
                    }
                }
            }
        } catch (error) {
            console.error('Error updating cart count:', error);
        }
    }

    // Global BASE_URL for JavaScript
    window.BASE_URL = '<?= BASE_URL ?>';

    // Add to cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart')) {
            const button = e.target.closest('.add-to-cart');
            const productId = button.dataset.productId;
            addToCart(productId, button);
        }
    });

    async function addToCart(productId, button) {
        const originalText = button.innerHTML;
        
        // Show loading
        button.innerHTML = '<span class="loading-spinner"></span>';
        button.disabled = true;

        try {
            const response = await fetch(`${BASE_URL}/api/cart/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            const result = await response.json();
            
            if (result.success) {
                showToast('success', result.message);
                updateCartCount();
            } else {
                showToast('danger', result.message);
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showToast('danger', 'Lỗi kết nối mạng');
        } finally {
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    // Price formatting
    window.formatPrice = function(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    // Form validation helper
    window.validateForm = function(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    };
});

// Utility functions
const Utils = {
    // Debounce function for search
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Format file size
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    // Get URL parameters
    getUrlParams: function() {
        const params = {};
        window.location.search.substring(1).split('&').forEach(param => {
            const [key, value] = param.split('=');
            if (key) params[key] = decodeURIComponent(value || '');
        });
        return params;
    }
};