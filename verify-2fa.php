<?php
// verify-2fa.php - 2FA Code Verification Page
session_start();
require_once 'includes/two-factor-auth.php';
require_once 'config/database.php';

$database = new Database();
$twoFA = new TwoFactorAuth();
$error = '';
$success = '';

// Check if user is in the 2FA verification step
if (!isset($_SESSION['2fa_pending_user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle 2FA code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $use_backup = isset($_POST['use_backup']) ? true : false;
    $user_id = $_SESSION['2fa_pending_user_id'];

    // Get user data from database
    $query = "SELECT id, name, email, two_fa_secret, two_fa_backup_codes FROM users WHERE id = ?";
    $stmt = $database->executeQuery($query, [$user_id]);
    $user = $database->fetchOne($stmt);

    if (!$user) {
        $error = 'User not found';
    } elseif ($use_backup) {
        // Verify backup code
        $backupCodes = $user['two_fa_backup_codes'] ? json_decode($user['two_fa_backup_codes'], true) : [];
        $result = $twoFA->verifyBackupCode($code, $backupCodes);

        if ($result['valid']) {
            // Update remaining backup codes
            $query = "UPDATE users SET two_fa_backup_codes = ? WHERE id = ?";
            $newCodesJSON = !empty($result['remaining_codes']) ? json_encode($result['remaining_codes']) : null;
            $database->executeQuery($query, [$newCodesJSON, $user_id]);

            // Clear 2FA session and mark as authenticated
            unset($_SESSION['2fa_pending_user_id']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Create auth token
            require_once 'includes/jwt.php';
            $token = JWT::generate($user['id'], $user['email']);
            setcookie('auth_token', $token, time() + 86400, '/', '', false, true);

            // Log the backup code usage
            error_log("User {$user['id']} used a backup code for 2FA verification");

            $success = 'Authentication successful! Redirecting...';
            header('Refresh: 2; URL=game.php');
        } else {
            $error = 'Invalid backup code';
        }
    } else {
        // Verify TOTP code
        if ($twoFA->verifyCode($user['two_fa_secret'], $code)) {
            // Clear 2FA session and mark as authenticated
            unset($_SESSION['2fa_pending_user_id']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Create auth token
            require_once 'includes/jwt.php';
            $token = JWT::generate($user['id'], $user['email']);
            setcookie('auth_token', $token, time() + 86400, '/', '', false, true);

            header('Location: game.php');
            exit();
        } else {
            $error = 'Invalid authentication code. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--accent-purple);
        }

        .tab-button {
            padding: 10px 20px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: var(--glow-color);
            border-bottom: 3px solid var(--glow-color);
            margin-bottom: -2px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .code-input {
            letter-spacing: 10px;
            font-size: 2em;
            text-align: center;
            font-weight: bold;
            font-family: monospace;
        }

        .backup-code-input {
            font-family: monospace;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🔐 Two-Factor Authentication</h1>
            <p style="color: var(--text-secondary); margin-top: 10px;">Verify your identity to continue</p>
        </div>

        <div class="card fade-in" style="max-width: 500px; margin: 0 auto;">
            <?php if ($error): ?>
                <div style="background: linear-gradient(135deg, var(--danger-color), #d63031); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: linear-gradient(135deg, var(--success-color), #20bf6b); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    ✅ <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Tab Navigation -->
            <div class="tabs">
                <button class="tab-button active" data-tab="authenticator">
                    📱 Authenticator App
                </button>
                <button class="tab-button" data-tab="backup">
                    🔑 Backup Code
                </button>
            </div>

            <!-- Authenticator App Tab -->
            <form id="authenticatorForm" method="POST" action="verify-2fa.php" class="tab-content active">
                <div class="form-group">
                    <label for="code">Enter the 6-digit code from your authenticator app:</label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control code-input"
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autocomplete="off"
                        autofocus
                    >
                    <small style="color: var(--text-secondary); display: block; margin-top: 10px; text-align: center;">
                        Open your authenticator app and enter the code
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    ✓ Verify Code
                </button>
            </form>

            <!-- Backup Code Tab -->
            <form id="backupForm" method="POST" action="verify-2fa.php" class="tab-content" style="display: none;">
                <div class="form-group">
                    <label for="backup_code">Enter a backup code:</label>
                    <input
                        type="text"
                        id="backup_code"
                        name="code"
                        class="form-control backup-code-input"
                        placeholder="XXXX-XXXX-XXXX"
                        required
                        autocomplete="off"
                    >
                    <small style="color: var(--text-secondary); display: block; margin-top: 10px;">
                        Use one of your backup codes if you don't have access to your authenticator app
                    </small>
                </div>

                <input type="hidden" name="use_backup" value="1">

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    ✓ Verify Backup Code
                </button>
            </form>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--accent-purple);">
                <a href="logout.php" style="color: var(--glow-color); text-decoration: none; font-size: 1em;">
                    ⬅️ Start Over
                </a>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                const tabForm = document.getElementById(tabName + 'Form');
                if (tabForm) {
                    tabForm.classList.add('active');
                }
            });
        });

        // Auto-focus on code input
        document.getElementById('code').addEventListener('input', function() {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                setTimeout(() => {
                    document.getElementById('authenticatorForm').submit();
                }, 100);
            }
        });

        // Format backup code input
        document.getElementById('backup_code').addEventListener('input', function() {
            let value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase();
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            if (value.length > 9) {
                value = value.substring(0, 9) + '-' + value.substring(9, 12);
            }
            this.value = value.substring(0, 12);
        });
    </script>
</body>
</html>
