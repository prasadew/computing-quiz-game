<?php
// login.php - User Login
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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = $auth->login($email, $password);
    
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🔐 Login to Play</h1>
        </div>

        <div class="card fade-in" style="max-width: 500px; margin: 0 auto;">
            <?php if ($error): ?>
                <div style="background: linear-gradient(135deg, var(--danger-color), #d63031); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 1.1em; animation: shake 0.5s ease;">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: linear-gradient(135deg, var(--success-color), #20bf6b); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 1.1em;">
                    ✅ <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" id="loginForm">
                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        required 
                        placeholder="your.email@example.com"
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
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    🎮 Login & Start Playing
                </button>
            </form>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--accent-purple);">
                <p style="font-size: 1.1em; color: var(--text-secondary); margin-bottom: 15px;">
                    Don't have an account?
                </p>
                <a href="register.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">
                    📝 Register Now
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: var(--glow-color); text-decoration: none; font-size: 1.1em;">
                    ⬅️ Back to Home
                </a>
            </div>
        </form>
    </div>

    <script>
        // Add animation to form
        const form = document.getElementById('loginForm');
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = '<span class="loading"></span> Logging in...';
            button.disabled = true;
        });

        // Form validation
        document.getElementById('email').addEventListener('input', function(e) {
            if (!e.target.validity.valid) {
                e.target.style.borderColor = 'var(--danger-color)';
            } else {
                e.target.style.borderColor = 'var(--success-color)';
            }
        });
    </script>
</body>
</html>