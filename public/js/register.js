document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const roleSelect = document.getElementById('role');
    const agentFields = document.getElementById('agentFields');
    const adminFields = document.getElementById('adminFields');

    // Handle role selection change
    roleSelect.addEventListener('change', () => {
        const selectedRole = roleSelect.value;
        
        // Hide all role-specific fields first
        agentFields.classList.add('hidden');
        adminFields.classList.add('hidden');
        
        // Show fields based on selected role
        if (selectedRole === 'agent') {
            agentFields.classList.remove('hidden');
            // Make agent fields required
            document.getElementById('agentId').required = true;
            document.getElementById('department').required = true;
            document.getElementById('adminCode').required = false;
        } else if (selectedRole === 'admin') {
            adminFields.classList.remove('hidden');
            // Make admin fields required
            document.getElementById('adminCode').required = true;
            document.getElementById('agentId').required = false;
            document.getElementById('department').required = false;
        } else {
            // Customer role - no additional fields required
            document.getElementById('agentId').required = false;
            document.getElementById('department').required = false;
            document.getElementById('adminCode').required = false;
        }
    });

    // Handle form submission
    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = registerForm.querySelector('button[type="submit"]');
        
        try {
            // Basic validation
            const password = registerForm.password.value;
            const confirmPassword = registerForm.confirmPassword.value;
            
            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }

            if (password.length < 8) {
                showError('Password must be at least 8 characters long');
                return;
            }

            // Prepare registration data
            const formData = {
                fullName: registerForm.fullName.value,
                email: registerForm.email.value,
                password: password,
                role: registerForm.role.value
            };

            // Add role-specific data
            if (formData.role === 'agent') {
                formData.agentId = registerForm.agentId.value;
                formData.department = registerForm.department.value;
            } else if (formData.role === 'admin') {
                formData.adminCode = registerForm.adminCode.value;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.classList.add('btn-loading');

            // Send registration request
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                // Store auth token
                localStorage.setItem('authToken', data.token);
                localStorage.setItem('userRole', data.role);
                
                // Show success message before redirect
                showSuccess('Registration successful! Redirecting...');
                
                // Redirect based on role after a short delay
                setTimeout(() => {
                    switch (data.role) {
                        case 'admin':
                            window.location.href = '/admin/dashboard';
                            break;
                        case 'agent':
                            window.location.href = '/agent/dashboard';
                            break;
                        default:
                            window.location.href = '/';
                    }
                }, 1500);
            } else {
                throw new Error(data.message || 'Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            showError(error.message || 'Failed to register. Please try again.');
            // Reset button state
            submitButton.disabled = false;
            submitButton.classList.remove('btn-loading');
        }
    });

    // Helper functions for showing messages
    function showError(message) {
        // Remove any existing error messages
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        registerForm.insertBefore(errorDiv, registerForm.querySelector('button'));
    }

    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = message;
        registerForm.insertBefore(successDiv, registerForm.querySelector('button'));
    }
}); 