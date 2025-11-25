/**
 * Validation functions for the bakery website
 */

class FormValidator {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.options = Object.assign({
            realTime: true,
            showErrors: true,
            errorClass: 'is-invalid',
            successClass: 'is-valid'
        }, options);
        
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        if (this.options.realTime) {
            this.setupRealTimeValidation();
        }
        
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });
    }
    
    setupRealTimeValidation() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            // Clear validation on input
            input.addEventListener('input', () => {
                this.clearFieldValidation(input);
            });
        });
    }
    
    validateForm() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    validateField(field) {
        const value = field.value.trim();
        const rules = field.dataset.rules ? field.dataset.rules.split('|') : [];
        let isValid = true;
        
        // Clear previous validation
        this.clearFieldValidation(field);
        
        for (const rule of rules) {
            const [ruleName, ruleValue] = rule.split(':');
            
            switch (ruleName) {
                case 'required':
                    if (!value) {
                        this.showError(field, 'Trường này là bắt buộc');
                        isValid = false;
                    }
                    break;
                    
                case 'email':
                    if (value && !this.isValidEmail(value)) {
                        this.showError(field, 'Email không hợp lệ');
                        isValid = false;
                    }
                    break;
                    
                case 'min':
                    if (value && value.length < parseInt(ruleValue)) {
                        this.showError(field, `Tối thiểu ${ruleValue} ký tự`);
                        isValid = false;
                    }
                    break;
                    
                case 'max':
                    if (value && value.length > parseInt(ruleValue)) {
                        this.showError(field, `Tối đa ${ruleValue} ký tự`);
                        isValid = false;
                    }
                    break;
                    
                case 'numeric':
                    if (value && !this.isNumeric(value)) {
                        this.showError(field, 'Chỉ được nhập số');
                        isValid = false;
                    }
                    break;
                    
                case 'phone':
                    if (value && !this.isValidPhone(value)) {
                        this.showError(field, 'Số điện thoại không hợp lệ');
                        isValid = false;
                    }
                    break;
                    
                case 'match':
                    const matchField = document.getElementById(ruleValue);
                    if (value && matchField && value !== matchField.value) {
                        this.showError(field, 'Giá trị không khớp');
                        isValid = false;
                    }
                    break;
            }
            
            if (!isValid) break;
        }
        
        if (isValid && value && this.options.showErrors) {
            this.showSuccess(field);
        }
        
        return isValid;
    }
    
    showError(field, message) {
        field.classList.add(this.options.errorClass);
        field.classList.remove(this.options.successClass);
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    
    showSuccess(field) {
        field.classList.add(this.options.successClass);
        field.classList.remove(this.options.errorClass);
    }
    
    clearFieldValidation(field) {
        field.classList.remove(this.options.errorClass);
        field.classList.remove(this.options.successClass);
        
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Validation methods
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    isValidPhone(phone) {
        const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/;
        return phoneRegex.test(phone);
    }
    
    isNumeric(value) {
        return /^\d+$/.test(value);
    }
}

// Global validation functions
const ValidationHelper = {
    // Validate email
    validateEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // Validate phone (Vietnamese format)
    validatePhone: function(phone) {
        const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/;
        return phoneRegex.test(phone);
    },
    
    // Validate password strength
    validatePassword: function(password) {
        return password.length >= 6;
    },
    
    // Validate required fields
    validateRequired: function(fields) {
        let isValid = true;
        fields.forEach(field => {
            if (!field.value.trim()) {
                this.markFieldError(field, 'Trường này là bắt buộc');
                isValid = false;
            }
        });
        return isValid;
    },
    
    // Mark field as error
    markFieldError: function(field, message) {
        field.classList.add('is-invalid');
        
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.textContent = message;
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    },
    
    // Clear field error
    clearFieldError: function(field) {
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    },
    
    // Validate file upload
    validateFile: function(file, options = {}) {
        const defaults = {
            maxSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['jpg', 'jpeg', 'png', 'gif', 'webp']
        };
        
        options = Object.assign(defaults, options);
        
        if (file.size > options.maxSize) {
            return { isValid: false, message: `File quá lớn. Tối đa: ${options.maxSize / 1024 / 1024}MB` };
        }
        
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!options.allowedTypes.includes(fileExtension)) {
            return { isValid: false, message: `Loại file không được hỗ trợ. Chấp nhận: ${options.allowedTypes.join(', ')}` };
        }
        
        return { isValid: true };
    }
};

// Initialize validation for all forms with data-validate attribute
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        const formId = form.id || `form-${Math.random().toString(36).substr(2, 9)}`;
        if (!form.id) form.id = formId;
        
        new FormValidator(formId, {
            realTime: true,
            showErrors: true
        });
    });
    
    // Auto-format phone number
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value.substr(0, 10);
            } else if (value.startsWith('84')) {
                value = '0' + value.substr(2, 9);
            }
            e.target.value = value;
        });
    });
    
    // Auto-format currency
    const currencyInputs = document.querySelectorAll('input[data-currency]');
    currencyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('vi-VN');
            }
            e.target.value = value;
        });
        
        input.addEventListener('blur', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                e.target.value = parseInt(value).toLocaleString('vi-VN');
            }
        });
        
        input.addEventListener('focus', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value || '';
        });
    });
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { FormValidator, ValidationHelper };
}