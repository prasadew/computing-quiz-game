<?php
// settings.php - User Settings and 2FA Management
session_start();
require_once 'includes/auth.php';
require_once 'includes/two-factor-auth.php';
require_once 'config/database.php';

$auth = new Auth();
$database = new Database();
$twoFA = new TwoFactorAuth();

// Require authentication
$auth->requireAuth();

$user = $auth->getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$message = '';
$messageType = '';

// Get user's 2FA status
$query = "SELECT two_fa_enabled, two_fa_backup_codes FROM users WHERE id = ?";
$stmt = $database->executeQuery($query, [$user['id']]);
$userData = $database->fetchOne($stmt);
$twoFAEnabled = $userData['two_fa_enabled'] ?? false;
$backupCodes = $userData['two_fa_backup_codes'] ? json_decode($userData['two_fa_backup_codes'], true) : [];

// Handle disable 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'disable_2fa') {
        $password = $_POST['password'] ?? '';
        
        // Verify password
        if (empty($password)) {
            $error = 'Password is required to disable 2FA';
        } else {
            // Get user's password hash
            $query = "SELECT password_hash FROM users WHERE id = ?";
            $stmt = $database->executeQuery($query, [$user['id']]);
            $userData = $database->fetchOne($stmt);
            
            if (password_verify($password, $userData['password_hash'])) {
                // Disable 2FA
                $query = "UPDATE users SET two_fa_enabled = 0, two_fa_secret = NULL, two_fa_backup_codes = NULL WHERE id = ?";
                $database->executeQuery($query, [$user['id']]);
                
                $success = '2FA has been successfully disabled';
                $twoFAEnabled = false;
                $backupCodes = [];
            } else {
                $error = 'Invalid password. 2FA was not disabled.';
            }
        }
    } elseif ($_POST['action'] === 'regenerate_backup_codes') {
        // Check if 2FA is enabled
        if (!$twoFAEnabled) {
            $error = '2FA is not enabled. Please enable it first.';
        } else {
            $password = $_POST['password'] ?? '';
            
            // Verify password
            if (empty($password)) {
                $error = 'Password is required to regenerate backup codes';
            } else {
                // Get user's password hash
                $query = "SELECT password_hash FROM users WHERE id = ?";
                $stmt = $database->executeQuery($query, [$user['id']]);
                $userData = $database->fetchOne($stmt);
                
                if (password_verify($password, $userData['password_hash'])) {
                    // Generate new backup codes
                    $newBackupCodes = $twoFA->generateBackupCodes();
                    
                    // Update in database
                    $query = "UPDATE users SET two_fa_backup_codes = ? WHERE id = ?";
                    $codesJSON = json_encode($newBackupCodes);
                    $database->executeQuery($query, [$codesJSON, $user['id']]);
                    
                    $success = 'Backup codes have been regenerated. Save the new codes below.';
                    $backupCodes = $newBackupCodes;
                } else {
                    $error = 'Invalid password. Backup codes were not regenerated.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .settings-section {
            background: var(--bg-secondary);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent-purple);
        }

        .settings-section h2 {
            color: var(--glow-color);
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .settings-row:last-child {
            border-bottom: none;
        }

        .settings-info {
            flex: 1;
        }

        .settings-info h3 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }

        .settings-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-enabled {
            background: var(--success-color);
            color: white;
        }

        .status-disabled {
            background: var(--danger-color);
            color: white;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            border: 2px solid var(--accent-purple);
        }

        .modal-header {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: var(--glow-color);
        }

        .backup-codes-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 20px 0;
        }

        .backup-code-item {
            background: var(--bg-secondary);
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.85em;
            user-select: all;
            cursor: pointer;
            border: 1px solid var(--accent-purple);
            text-align: center;
        }

        .backup-code-item:hover {
            background: var(--accent-purple);
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            background: var(--bg-secondary);
            border: 1px solid var(--accent-purple);
            border-radius: 5px;
            color: var(--text-primary);
        }

        .warning-box {
            background: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>⚙️ Account Settings</h1>
            <p style="color: var(--text-secondary);">Manage your account and security settings</p>
        </div>

        <div class="settings-container fade-in">
            <?php if ($error): ?>
                <div style="background: linear-gradient(135deg, var(--danger-color), #d63031); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: linear-gradient(135deg, var(--success-color), #20bf6b); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    ✅ <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Account Info Section -->
            <div class="settings-section">
                <h2>👤 Account Information</h2>
                <div class="settings-row">
                    <div class="settings-info">
                        <h3>Name</h3>
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                </div>
                <div class="settings-row">
                    <div class="settings-info">
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
                <div class="settings-row">
                    <div class="settings-info">
                        <h3>Total Score</h3>
                        <p><?php echo number_format($user['total_score']); ?> points</p>
                    </div>
                </div>
            </div>

            <!-- Two-Factor Authentication Section -->
            <div class="settings-section">
                <h2>🔐 Two-Factor Authentication</h2>
                <div class="settings-row">
                    <div class="settings-info">
                        <h3>Status</h3>
                        <p>
                            <?php if ($twoFAEnabled): ?>
                                <span class="status-badge status-enabled">✓ Enabled</span>
                            <?php else: ?>
                                <span class="status-badge status-disabled">✗ Disabled</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="button-group">
                        <?php if ($twoFAEnabled): ?>
                            <button onclick="openModal('manageModal')" class="btn btn-secondary">
                                🛠️ Manage
                            </button>
                        <?php else: ?>
                            <a href="setup-2fa.php" class="btn btn-primary" style="text-decoration: none;">
                                🔒 Enable 2FA
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($twoFAEnabled): ?>
                    <div class="settings-row">
                        <div class="settings-info">
                            <h3>Backup Codes Available</h3>
                            <p><?php echo count($backupCodes); ?> codes remaining</p>
                        </div>
                        <?php if (count($backupCodes) <= 2): ?>
                            <div class="warning-box" style="margin: 0;">
                                ⚠️ Low on backup codes! Consider regenerating them.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="game.php" class="btn btn-primary" style="text-decoration: none;">
                    🎮 Back to Game
                </a>
                <a href="logout.php" class="btn btn-secondary" style="text-decoration: none; margin-left: 10px;">
                    🚪 Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Manage 2FA Modal -->
    <div id="manageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">🛠️ Manage 2FA</div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: var(--glow-color);">What would you like to do?</h3>
            </div>

            <button onclick="showRegenerateForm()" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                🔄 Regenerate Backup Codes
            </button>

            <button onclick="showDisableForm()" class="btn btn-danger" style="width: 100%; margin-bottom: 10px;">
                🔓 Disable 2FA
            </button>

            <button onclick="closeModal('manageModal')" class="btn btn-secondary" style="width: 100%;">
                ❌ Cancel
            </button>

            <!-- Regenerate Codes Form -->
            <div id="regenerateForm" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--accent-purple);">
                <h3 style="color: var(--glow-color);">Regenerate Backup Codes</h3>
                <p style="color: var(--text-secondary);">
                    This will generate new backup codes. Your old codes will no longer work.
                </p>
                <form method="POST" action="settings.php">
                    <div class="form-group">
                        <label for="regen_password">Confirm with your password:</label>
                        <input type="password" id="regen_password" name="password" required>
                    </div>
                    <input type="hidden" name="action" value="regenerate_backup_codes">
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                        ✓ Regenerate Codes
                    </button>
                </form>
                <button onclick="showOptions()" class="btn btn-secondary" style="width: 100%;">
                    ⬅️ Back
                </button>
            </div>

            <!-- Disable Form -->
            <div id="disableForm" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--accent-purple);">
                <h3 style="color: var(--danger-color);">Disable 2FA</h3>
                <div class="warning-box">
                    ⚠️ Disabling 2FA will reduce your account security. Anyone with your password can access your account.
                </div>
                <form method="POST" action="settings.php">
                    <div class="form-group">
                        <label for="disable_password">Confirm with your password:</label>
                        <input type="password" id="disable_password" name="password" required>
                    </div>
                    <input type="hidden" name="action" value="disable_2fa">
                    <button type="submit" class="btn btn-danger" style="width: 100%; margin-bottom: 10px;">
                        ✓ Disable 2FA
                    </button>
                </form>
                <button onclick="showOptions()" class="btn btn-secondary" style="width: 100%;">
                    ⬅️ Back
                </button>
            </div>
        </div>
    </div>

    <!-- Backup Codes Modal -->
    <div id="codesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">📋 New Backup Codes</div>
            <div class="warning-box">
                ⚠️ Save these codes in a secure place. You'll need them to access your account if you lose your authenticator.
            </div>
            <div class="backup-codes-list">
                <?php foreach ($backupCodes as $code): ?>
                    <div class="backup-code-item" title="Click to copy"><?php echo htmlspecialchars($code); ?></div>
                <?php endforeach; ?>
            </div>
            <button onclick="closeModal('codesModal')" class="btn btn-primary" style="width: 100%;">
                ✓ I've Saved My Codes
            </button>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function showOptions() {
            document.getElementById('regenerateForm').style.display = 'none';
            document.getElementById('disableForm').style.display = 'none';
        }

        function showRegenerateForm() {
            document.getElementById('regenerateForm').style.display = 'block';
            document.getElementById('disableForm').style.display = 'none';
        }

        function showDisableForm() {
            document.getElementById('disableForm').style.display = 'block';
            document.getElementById('regenerateForm').style.display = 'none';
        }

        // Copy backup codes on click
        document.querySelectorAll('.backup-code-item').forEach(item => {
            item.addEventListener('click', function() {
                const text = this.innerText;
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = this.innerText;
                    this.innerText = '✓ Copied!';
                    setTimeout(() => {
                        this.innerText = originalText;
                    }, 1500);
                });
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });

        // Show codes modal if codes were just generated
        <?php if ($success && strpos($success, 'regenerated') !== false): ?>
            setTimeout(() => {
                openModal('codesModal');
            }, 500);
        <?php endif; ?>
    </script>
</body>
</html>
