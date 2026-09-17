document.addEventListener('DOMContentLoaded', function() {

    const loginBtn = document.getElementById('loginBtn');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberCheck = document.getElementById('rememberMe');
    const feedbackDiv = document.getElementById('loginFeedback');

    // --- 1. Restore remembered username on page load (if any) ---
    const savedUsername = localStorage.getItem('rememberedUsername');
    if (savedUsername) {
        usernameInput.value = savedUsername;
        rememberCheck.checked = true;
    }

    // --- 2. Check if user is already logged in (persistent) ---
    if (localStorage.getItem('isLoggedIn') === 'true') {
        window.location.href = 'dashboard.html';
    }

    // --- 3. Login button logic ---
    loginBtn.addEventListener('click', function() {
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        feedbackDiv.innerHTML = '';

        if (username === '' || password === '') {
            showFeedback('Please enter both username and password.', 'danger');
            return;
        }

        const validUsername = 'admin';
        const validPassword = 'password123';

        if (username === validUsername && password === validPassword) {
            // --- Handle "Remember Me" ---
            if (rememberCheck.checked) {
                // Save the username for future visits
                localStorage.setItem('rememberedUsername', username);
            } else {
                // Clear any previously saved username
                localStorage.removeItem('rememberedUsername');
            }

            // Set login state (persistent across browser sessions)
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('user', username);

            showFeedback('Login successful! Redirecting...', 'success');

            setTimeout(function() {
                window.location.href = 'dashboard.html';
            }, 1000);
        } else {
            showFeedback('Invalid username or password. Please try again.', 'danger');
        }
    });

    // --- 4. Helper function for feedback alerts ---
    function showFeedback(message, type) {
        const alertClass = type === 'danger' ? 'alert-danger' : 'alert-success';
        feedbackDiv.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // --- 5. Enter key support ---
    usernameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') loginBtn.click();
    });
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') loginBtn.click();
    });

});