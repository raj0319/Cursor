// Vehicle Booking System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeNavbar();
    initializeForms();
    initializeBookingSystem();
    initializeAnimations();
    initializeTooltips();
});

// Navbar functionality
function initializeNavbar() {
    const navbar = document.querySelector('.navbar');
    
    // Add scroll effect to navbar
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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

// Form initialization and validation
function initializeForms() {
    // Add custom validation styles
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Real-time validation for specific fields
    initializeFieldValidation();
}

// Field-specific validation
function initializeFieldValidation() {
    // Email validation
    const emailFields = document.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateEmail(this);
        });
    });
    
    // Phone validation
    const phoneFields = document.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(field => {
        field.addEventListener('blur', function() {
            validatePhone(this);
        });
    });
    
    // Date validation
    const dateFields = document.querySelectorAll('input[type="date"]');
    dateFields.forEach(field => {
        field.addEventListener('change', function() {
            validateDate(this);
        });
    });
}

// Email validation function
function validateEmail(field) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(field.value);
    
    toggleFieldValidation(field, isValid, 'Please enter a valid email address');
    return isValid;
}

// Phone validation function
function validatePhone(field) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    const isValid = phoneRegex.test(field.value.replace(/\s/g, ''));
    
    toggleFieldValidation(field, isValid, 'Please enter a valid phone number');
    return isValid;
}

// Date validation function
function validateDate(field) {
    const selectedDate = new Date(field.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const isValid = selectedDate >= today;
    toggleFieldValidation(field, isValid, 'Date cannot be in the past');
    return isValid;
}

// Toggle field validation styles
function toggleFieldValidation(field, isValid, errorMessage) {
    const feedback = field.parentNode.querySelector('.invalid-feedback') || 
                    createInvalidFeedback(field.parentNode, errorMessage);
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        feedback.style.display = 'none';
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        feedback.textContent = errorMessage;
        feedback.style.display = 'block';
    }
}

// Create invalid feedback element
function createInvalidFeedback(parent, message) {
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    parent.appendChild(feedback);
    return feedback;
}

// Booking system functionality
function initializeBookingSystem() {
    // Date range validation for booking forms
    const startDateInputs = document.querySelectorAll('input[name="start_date"]');
    const endDateInputs = document.querySelectorAll('input[name="end_date"]');
    
    startDateInputs.forEach((startDate, index) => {
        const endDate = endDateInputs[index];
        if (endDate) {
            startDate.addEventListener('change', function() {
                updateEndDateMin(startDate, endDate);
                calculateBookingTotal();
            });
            
            endDate.addEventListener('change', function() {
                calculateBookingTotal();
            });
        }
    });
    
    // Vehicle availability checker
    initializeAvailabilityChecker();
    
    // Booking form submission
    initializeBookingFormSubmission();
}

// Update minimum end date based on start date
function updateEndDateMin(startDateField, endDateField) {
    const startDate = new Date(startDateField.value);
    startDate.setDate(startDate.getDate() + 1); // Minimum 1 day rental
    
    const minEndDate = startDate.toISOString().split('T')[0];
    endDateField.min = minEndDate;
    
    // Clear end date if it's now invalid
    if (endDateField.value && new Date(endDateField.value) < startDate) {
        endDateField.value = '';
    }
}

// Calculate booking total
function calculateBookingTotal() {
    const startDateField = document.querySelector('input[name="start_date"]');
    const endDateField = document.querySelector('input[name="end_date"]');
    const pricePerDayElement = document.querySelector('[data-price-per-day]');
    const totalElement = document.querySelector('.booking-total');
    
    if (startDateField && endDateField && pricePerDayElement && totalElement) {
        const startDate = new Date(startDateField.value);
        const endDate = new Date(endDateField.value);
        const pricePerDay = parseFloat(pricePerDayElement.dataset.pricePerDay);
        
        if (startDate && endDate && endDate > startDate) {
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const total = days * pricePerDay;
            
            totalElement.innerHTML = `
                <div class="booking-summary">
                    <p><strong>Duration:</strong> ${days} day${days > 1 ? 's' : ''}</p>
                    <p><strong>Price per day:</strong> $${pricePerDay.toFixed(2)}</p>
                    <p class="total-amount"><strong>Total Amount: $${total.toFixed(2)}</strong></p>
                </div>
            `;
        } else {
            totalElement.innerHTML = '';
        }
    }
}

// Vehicle availability checker
function initializeAvailabilityChecker() {
    const checkAvailabilityBtn = document.querySelector('.check-availability');
    
    if (checkAvailabilityBtn) {
        checkAvailabilityBtn.addEventListener('click', function() {
            const vehicleId = this.dataset.vehicleId;
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            
            if (vehicleId && startDate && endDate) {
                checkVehicleAvailability(vehicleId, startDate, endDate);
            } else {
                showNotification('Please select both start and end dates', 'warning');
            }
        });
    }
}

// Check vehicle availability via AJAX
function checkVehicleAvailability(vehicleId, startDate, endDate) {
    showLoadingSpinner();
    
    fetch('/check-availability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            vehicle_id: vehicleId,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.available) {
            showNotification('Vehicle is available for selected dates!', 'success');
            updateBookingTotal(data);
        } else {
            showNotification('Vehicle is not available for selected dates', 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        showNotification('Error checking availability', 'error');
        console.error('Error:', error);
    });
}

// Update booking total display
function updateBookingTotal(data) {
    const totalElement = document.querySelector('.booking-total');
    if (totalElement) {
        totalElement.innerHTML = `
            <div class="availability-result success">
                <i class="fas fa-check-circle"></i>
                <p><strong>Available!</strong></p>
                <p>Duration: ${data.total_days} day${data.total_days > 1 ? 's' : ''}</p>
                <p>Price per day: $${data.price_per_day}</p>
                <p class="total-amount"><strong>Total: ${data.formatted_total}</strong></p>
            </div>
        `;
    }
}

// Booking form submission
function initializeBookingFormSubmission() {
    const bookingForms = document.querySelectorAll('.booking-form');
    
    bookingForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateBookingForm(form)) {
                submitBookingForm(form);
            }
        });
    });
}

// Validate booking form
function validateBookingForm(form) {
    const startDate = form.querySelector('input[name="start_date"]').value;
    const endDate = form.querySelector('input[name="end_date"]').value;
    
    if (!startDate || !endDate) {
        showNotification('Please select both start and end dates', 'error');
        return false;
    }
    
    if (new Date(startDate) >= new Date(endDate)) {
        showNotification('End date must be after start date', 'error');
        return false;
    }
    
    return true;
}

// Submit booking form via AJAX
function submitBookingForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            showNotification(data.message || 'An error occurred', 'error');
        }
    })
    .catch(error => {
        showNotification('Network error occurred', 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Animation initialization
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.vehicle-card, .feature-card, .stat-card').forEach(el => {
        observer.observe(el);
    });
    
    // Counter animation for statistics
    initializeCounterAnimation();
}

// Counter animation for statistics
function initializeCounterAnimation() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/\D/g, ''));
        const suffix = counter.textContent.replace(/[\d]/g, '');
        
        let current = 0;
        const increment = target / 50;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current) + suffix;
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target + suffix;
            }
        };
        
        // Start animation when element is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
}

// Initialize tooltips
function initializeTooltips() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${getAlertClass(type)} alert-dismissible fade show`;
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Position notification
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '400px';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Get alert class for notification type
function getAlertClass(type) {
    switch (type) {
        case 'success': return 'success';
        case 'error': return 'danger';
        case 'warning': return 'warning';
        default: return 'info';
    }
}

// Get icon for notification type
function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

// Loading spinner functions
function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.id = 'loading-spinner';
    spinner.className = 'loading-spinner';
    spinner.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    
    // Style the spinner overlay
    spinner.style.position = 'fixed';
    spinner.style.top = '0';
    spinner.style.left = '0';
    spinner.style.width = '100%';
    spinner.style.height = '100%';
    spinner.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    spinner.style.display = 'flex';
    spinner.style.alignItems = 'center';
    spinner.style.justifyContent = 'center';
    spinner.style.zIndex = '9999';
    
    document.body.appendChild(spinner);
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// Utility functions
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global use
window.VehicleBookingSystem = {
    showNotification,
    showLoadingSpinner,
    hideLoadingSpinner,
    validateEmail,
    validatePhone,
    validateDate,
    checkVehicleAvailability
};