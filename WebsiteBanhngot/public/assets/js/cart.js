// Cart-specific JavaScript functionality

class CartPage {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.init();
    }
    
    init() {
        this.setupQuantityControls();
        this.setupRemoveButtons();
        this.setupCheckoutForm();
        this.calculateTotals();
    }
    
    setupQuantityControls() {
        document.querySelectorAll('.quantity-minus').forEach(button => {
            button.addEventListener('click', (e) => {
                const cartId = e.target.closest('.input-group').querySelector('.quantity-input').dataset.cartId;
                const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
                let value = parseInt(input.value);
                if (value > 1) {
                    this.updateQuantity(cartId, value - 1);
                }
            });
        });
        
        document.querySelectorAll('.quantity-plus').forEach(button => {
            button.addEventListener('click', (e) => {
                const cartId = e.target.closest('.input-group').querySelector('.quantity-input').dataset.cartId;
                const input = document.querySelector(`.quantity-input[data-cart-id="${cartId}"]`);
                let value = parseInt(input.value);
                let max = parseInt(input.max);
                if (value < max) {
                    this.updateQuantity(cartId, value + 1);
                }
            });
        });
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const cartId = e.target.dataset.cartId;
                let value = parseInt(e.target.value);
                let max = parseInt(e.target.max);
                let min = parseInt(e.target.min);
                
                if (value < min) value = min;
                if (value > max) value = max;
                
                e.target.value = value;
                this.updateQuantity(cartId, value);
            });
        });
    }
    
    setupRemoveButtons() {
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                const cartId = e.target.closest('.remove-item').dataset.cartId;
                const productName = e.target.closest('tr').querySelector('h6').textContent;
                
                if (confirm(`Bạn có chắc muốn xóa "${productName}" khỏi giỏ hàng?`)) {
                    this.removeItem(cartId);
                }
            });
        });
    }
    
    setupCheckoutForm() {
        const form = document.getElementById('checkoutForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                this.validateCheckoutForm(e);
            });
        }
        
        // Real-time form validation
        const inputs = document.querySelectorAll('#checkoutForm input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    }
    
    validateField(field) {
        const value = field.value.trim();
        const errorElement = field.parentNode.querySelector('.invalid-feedback') || 
                           this.createErrorElement(field);
        
        if (!value) {
            this.showFieldError(field, errorElement, 'Trường này là bắt buộc');
            return false;
        }
        
        if (field.type === 'email' && !isValidEmail(value)) {
            this.showFieldError(field, errorElement, 'Email không hợp lệ');
            return false;
        }
        
        if (field.name === 'customer_phone' && !isValidPhone(value)) {
            this.showFieldError(field, errorElement, 'Số điện thoại không hợp lệ');
            return false;
        }
        
        this.clearFieldError(field, errorElement);
        return true;
    }
    
    createErrorElement(field) {
        const errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        field.parentNode.appendChild(errorElement);
        return errorElement;
    }
    
    showFieldError(field, errorElement, message) {
        field.classList.add('is-invalid');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    clearFieldError(field, errorElement) {
        field.classList.remove('is-invalid');
        errorElement.style.display = 'none';
    }
    
    validateCheckoutForm(e) {
        let isValid = true;
        const requiredFields = document.querySelectorAll('#checkoutForm input[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        const pickupTime = document.querySelector('input[name="pickup_time"]');
        if (pickupTime) {
            const selectedTime = new Date(pickupTime.value);
            const now = new Date();
            
            if (selectedTime <= now) {
                this.showFieldError(pickupTime, 
                    pickupTime.parentNode.querySelector('.invalid-feedback') || this.createErrorElement(pickupTime),
                    'Thời gian lấy hàng phải sau thời điểm hiện tại'
                );
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Vui lòng kiểm tra lại thông tin đơn hàng', 'error');
        }
    }
    
    async updateQuantity(cartId, quantity) {
        try {
            const response = await fetch(`${BASE_URL}/cart/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}&quantity=${quantity}&csrf_token=${this.csrfToken}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update cart count in navbar
                cartManager.updateCartCount(data.cart_count);
                
                // Reload page to update totals
                location.reload();
            } else {
                showNotification(data.message, 'error');
                // Reload to get correct quantities
                location.reload();
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            showNotification('Có lỗi xảy ra khi cập nhật số lượng', 'error');
        }
    }
    
    async removeItem(cartId) {
        try {
            const response = await fetch(`${BASE_URL}/cart/remove/${cartId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${this.csrfToken}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                cartManager.updateCartCount(data.cart_count);
                showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
                
                // Remove row from table
                const row = document.querySelector(`[data-cart-id="${cartId}"]`).closest('tr');
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.height = '0';
                    row.style.overflow = 'hidden';
                    
                    setTimeout(() => {
                        row.remove();
                        this.calculateTotals();
                        
                        // If cart is empty, show empty message
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            this.showEmptyCart();
                        }
                    }, 300);
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showNotification('Có lỗi xảy ra khi xóa sản phẩm', 'error');
        }
    }
    
    calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('tbody tr').forEach(row => {
            const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace(/[^\d]/g, ''));
            const quantity = parseInt(row.querySelector('.quantity-input').value);
            const total = price * quantity;
            
            // Update row total
            const totalCell = row.querySelector('td:nth-child(4)');
            totalCell.textContent = formatPrice(total);
            
            subtotal += total;
        });
        
        // Update subtotal in footer
        const subtotalElement = document.querySelector('tfoot .text-danger');
        if (subtotalElement) {
            subtotalElement.textContent = formatPrice(subtotal);
        }
    }
    
    showEmptyCart() {
        const tableBody = document.querySelector('tbody');
        const tableFooter = document.querySelector('tfoot');
        const checkoutButton = document.querySelector('a[href*="/checkout"]');
        
        if (tableBody && tableFooter) {
            tableBody.innerHTML = '';
            tableFooter.style.display = 'none';
            
            const emptyMessage = document.createElement('tr');
            emptyMessage.innerHTML = `
                <td colspan="5" class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Giỏ hàng trống</h4>
                    <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để bắt đầu mua sắm.</p>
                    <a href="${BASE_URL}/products" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                    </a>
                </td>
            `;
            tableBody.appendChild(emptyMessage);
            
            if (checkoutButton) {
                checkoutButton.style.display = 'none';
            }
        }
    }
}

// Initialize cart page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.cart-page')) {
        new CartPage();
    }
});

// Add to cart functionality for product pages
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForms = document.querySelectorAll('form.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const productId = formData.get('product_id');
            const quantity = formData.get('quantity') || 1;
            
            const success = await cartManager.addToCart(productId, quantity);
            if (success) {
                // Optional: Show cart preview or sidebar
                this.showCartPreview();
            }
        });
    });
    
    // Quick add to cart buttons
    document.querySelectorAll('.quick-add-to-cart').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const productId = button.dataset.productId;
            const quantity = 1;
            
            const success = await cartManager.addToCart(productId, quantity);
            if (success) {
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-cart-plus"></i>';
                    button.classList.remove('btn-success');
                }, 2000);
            }
        });
    });
});

// Cart preview/sidebar functionality
function showCartPreview() {
    // This could be implemented as a sidebar cart preview
    // For now, we'll just show a notification
    console.log('Cart preview would show here');
}

// Stock validation
function validateStock(productId, requestedQuantity) {
    // This would typically make an API call to check stock
    // For now, we'll assume it's handled server-side
    return true;
}