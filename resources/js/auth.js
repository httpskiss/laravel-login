/**
 * Authentication Page JavaScript
 * Handles login/registration form interactions
 */

/**
 * Toggle visibility of password input field
 * @param {string} fieldId - The ID of the password field
 * @param {HTMLElement} icon - The icon element to toggle
 */
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
}

/**
 * Flip between login and registration forms
 * Toggles the 'flipped' class on the form container
 */
function flipForm() {
    const formFlip = document.getElementById('formFlip');
    if (formFlip) {
        formFlip.classList.toggle('flipped');
    }
}

/**
 * Auto-flip to register form if there are registration errors
 * Runs on page load to show the appropriate form based on validation errors
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if any of the registration form errors exist
    const registrationErrors = [
        'first_name',
        'last_name',
        'email',
        'password',
        'employee_id',
        'department',
        'terms'
    ];

    // Check if the page has any registration error elements
    const hasRegistrationErrors = registrationErrors.some(field => {
        const errorElement = document.querySelector(`[data-error-field="${field}"]`);
        return errorElement !== null;
    });

    // Alternative check: look for error messages in registration fields
    const registerEmailField = document.getElementById('registerEmail');
    const firstNameField = document.getElementById('first_name');
    
    if (hasRegistrationErrors || 
        (registerEmailField && registerEmailField.parentElement.querySelector('.error-message')) ||
        (firstNameField && firstNameField.parentElement.querySelector('.error-message'))) {
        flipForm();
    }
});
