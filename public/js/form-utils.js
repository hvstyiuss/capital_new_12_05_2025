/**
 * Centralized Form Utilities
 * Handles form validation, submission, error display, and loading states
 */

class FormUtils {
    /**
     * Get CSRF token from meta tag
     */
    static getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Initialize form validation (prevent browser validation tooltips)
     */
    static initFormValidation(form) {
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('invalid', function(e) {
                e.preventDefault();
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                
                // Hide Laravel error messages for this field
                const laravelError = this.parentElement?.querySelector('.text-danger.small');
                if (laravelError) {
                    laravelError.style.display = 'none';
                }
            });
            
            // Clear custom validity on input/change
            field.addEventListener('input', function() {
                this.setCustomValidity('');
            });
            field.addEventListener('change', function() {
                this.setCustomValidity('');
            });
        });
    }

    /**
     * Show field error
     */
    static showFieldError(field, errorDiv, message) {
        if (field) {
            field.classList.add('is-invalid');
        }
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        }
    }

    /**
     * Clear field error
     */
    static clearFieldError(field, errorDiv) {
        if (field) {
            field.classList.remove('is-invalid');
        }
        if (errorDiv) {
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
        }
    }

    /**
     * Hide Laravel error messages
     */
    static hideLaravelErrors(selectors = ['.text-danger.small']) {
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(error => {
                error.style.display = 'none';
            });
        });
    }

    /**
     * Set loading state for submit button
     */
    static setLoadingState(submitBtn, submitIcon, isLoading) {
        if (submitBtn) {
            submitBtn.disabled = isLoading;
        }
        if (submitIcon) {
            if (isLoading) {
                submitIcon.className = 'fas fa-spinner fa-spin me-2';
            } else {
                submitIcon.className = 'fas fa-paper-plane me-2';
            }
        }
    }

    /**
     * Submit form via fetch API
     */
    static async submitForm(url, method, data, options = {}) {
        const {
            onSuccess = null,
            onError = null,
            onValidationError = null,
            showLoading = true,
            submitBtn = null,
            submitIcon = null
        } = options;

        // Set loading state
        if (showLoading) {
            this.setLoadingState(submitBtn, submitIcon, true);
        }

        try {
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'Accept': 'application/json',
            };

            const response = await fetch(url, {
                method: method,
                headers: headers,
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                if (onSuccess) {
                    onSuccess(result);
                }
                return { success: true, data: result };
            } else {
                // Handle validation errors
                if (result.errors && onValidationError) {
                    onValidationError(result.errors);
                } else if (result.message) {
                    alert(result.message);
                }
                
                if (onError) {
                    onError(result);
                }
                return { success: false, data: result };
            }
        } catch (error) {
            console.error('Form submission error:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
            
            if (onError) {
                onError(error);
            }
            return { success: false, error: error };
        } finally {
            // Reset loading state
            if (showLoading) {
                this.setLoadingState(submitBtn, submitIcon, false);
            }
        }
    }

    /**
     * Display validation errors from Laravel
     */
    static displayValidationErrors(errors, fieldMap = {}) {
        Object.keys(errors).forEach(field => {
            const errorMessages = errors[field];
            const errorMessage = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
            
            // Use field map if provided, otherwise try to find elements by ID
            const fieldElement = fieldMap[field]?.field || document.getElementById(field);
            const errorDivElement = fieldMap[field]?.errorDiv || document.getElementById(`${field}_error`);
            
            if (fieldElement) {
                fieldElement.classList.add('is-invalid');
            }
            if (errorDivElement) {
                errorDivElement.textContent = errorMessage;
                errorDivElement.classList.remove('d-none');
            }
        });
    }

    /**
     * Validate required field
     */
    static validateRequired(field, errorDiv, message = 'Ce champ est requis') {
        if (!field || !field.value.trim()) {
            this.showFieldError(field, errorDiv, message);
            return false;
        }
        this.clearFieldError(field, errorDiv);
        return true;
    }

    /**
     * Validate radio button group
     */
    static validateRadioGroup(radios, errorDiv, message = 'Veuillez faire une sélection') {
        const isChecked = Array.from(radios).some(radio => radio && radio.checked);
        if (!isChecked) {
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('d-none');
            }
            return false;
        }
        if (errorDiv) {
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
        }
        return true;
    }
}

// Make it globally available
window.FormUtils = FormUtils;

