document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

async function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const email = form.email.value;
    const password = form.password.value;
    const submitButton = form.querySelector('button[type="submit"]');
    
    try {
        submitButton.disabled = true;
        submitButton.textContent = 'Logging in...';
        
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Store auth token
            localStorage.setItem('authToken', data.token);
            // Redirect to home page or previous page
            window.location.href = '/';
        } else {
            throw new Error(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert(error.message || 'Failed to login. Please try again.');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Login';
    }
}

function checkAuthStatus() {
    const authToken = localStorage.getItem('authToken');
    return !!authToken;
}

function logout() {
    localStorage.removeItem('authToken');
    window.location.href = '/login';
} 