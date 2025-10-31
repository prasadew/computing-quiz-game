<?php
// register.php - User Registration
session_start();
require_once 'includes/auth.php';

$auth = new Auth();
$error = '';
$success = '';

// Redirect if already logged in
if ($auth->isAuthenticated()) {
    header('Location: game.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate password match
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = $auth->register($name, $email, $password);
        
        if ($result['success']) {
            // Set auth token in cookie
            setcookie('auth_token', $result['token'], time() + 86400, '/', '', false, true);
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['name'];
            
            header('Location: game.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>📝 Create Your Account</h1>
        </div>

        <div class="card fade-in" style="max-width: 500px; margin: 0 auto;">
            <?php if ($error): ?>
                <div style="background: linear-gradient(135deg, var(--danger-color), #d63031); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 1.1em; animation: shake 0.5s ease;">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" id="registerForm">
                <div class="form-group">
                    <label for="name">👤 Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        required 
                        placeholder="Enter your name"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        autocomplete="name"
                    >
                </div>

                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        required 
                        placeholder="your.email@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">🔒 Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        placeholder="Minimum 6 characters"
                        minlength="6"
                        autocomplete="new-password"
                    >
                    <small style="color: var(--text-secondary); font-size: 0.9em; display: block; margin-top: 5px;">
                        Password must be at least 6 characters long
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">🔒 Confirm Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        required 
                        placeholder="Re-enter your password"
                        minlength="6"
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    🚀 Register & Start Playing
                </button>
            </form>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--accent-purple);">
                <p style="font-size: 1.1em; color: var(--text-secondary); margin-bottom: 15px;">
                    Already have an account?
                </p>
                <a href="login.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">
                    🔐 Login Here
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: var(--glow-color); text-decoration: none; font-size: 1.1em;">
                    ⬅️ Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                confirmPassword.style.borderColor = 'var(--danger-color)';
                alert('Passwords do not match!');
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = '<span class="loading"></span> Creating account...';
            button.disabled = true;
        });

        // Real-time password match validation
        confirmPassword.addEventListener('input', function() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = 'var(--danger-color)';
            } else {
                confirmPassword.style.borderColor = 'var(--success-color)';
            }
        });

        // Email validation
        document.getElementById('email').addEventListener('input', function(e) {
            if (!e.target.validity.valid) {
                e.target.style.borderColor = 'var(--danger-color)';
            } else {
                e.target.style.borderColor = 'var(--success-color)';
            }
        });

        // Password strength indicator
        password.addEventListener('input', function() {
            const strength = password.value.length;
            if (strength < 6) {
                password.style.borderColor = 'var(--danger-color)';
            } else if (strength < 10) {
                password.style.borderColor = 'var(--warning-color)';
            } else {
                password.style.borderColor = 'var(--success-color)';
            }
        });
    </script>
</body>
</html>