<?php
// debug-2fa.php - Debug 2FA Status
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$database = new Database();

// Check if user is authenticated
if (!$auth->isAuthenticated()) {
    echo "<h1>Not Authenticated</h1>";
    echo "<p>Please <a href='login.php'>login</a> first.</p>";
    exit();
}

$user = $auth->getCurrentUser();

// Get detailed 2FA info
$query = "SELECT id, name, email, two_fa_enabled, two_fa_secret, two_fa_backup_codes FROM users WHERE id = ?";
$stmt = $database->executeQuery($query, [$user['id']]);
$userData = $database->fetchOne($stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Debug - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        .debug-box {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid var(--accent-purple);
            font-family: monospace;
            overflow-x: auto;
        }
        .label {
            color: var(--glow-color);
            font-weight: bold;
        }
        .value {
            color: var(--text-secondary);
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🔍 2FA Debug Information</h1>
        </div>

        <div class="card fade-in" style="max-width: 700px; margin: 0 auto;">
            <h2>User Information</h2>
            <div class="debug-box">
                <div><span class="label">User ID:</span> <span class="value"><?php echo $userData['id']; ?></span></div>
                <div><span class="label">Name:</span> <span class="value"><?php echo htmlspecialchars($userData['name']); ?></span></div>
                <div><span class="label">Email:</span> <span class="value"><?php echo htmlspecialchars($userData['email']); ?></span></div>
            </div>

            <h2>2FA Status</h2>
            <div class="debug-box">
                <div>
                    <span class="label">2FA Enabled:</span> 
                    <span class="value">
                        <?php if ($userData['two_fa_enabled']): ?>
                            <span style="color: var(--success-color);">✓ YES (1)</span>
                        <?php else: ?>
                            <span style="color: var(--danger-color);">✗ NO (0)</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div style="margin-top: 10px;">
                    <span class="label">2FA Secret:</span>
                    <span class="value">
                        <?php if ($userData['two_fa_secret']): ?>
                            <span style="color: var(--success-color);">Set (<?php echo strlen($userData['two_fa_secret']); ?> chars)</span>
                        <?php else: ?>
                            <span style="color: var(--danger-color);">Not Set</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div style="margin-top: 10px;">
                    <span class="label">Backup Codes:</span>
                    <span class="value">
                        <?php 
                            $codes = $userData['two_fa_backup_codes'] ? json_decode($userData['two_fa_backup_codes'], true) : [];
                            if (count($codes) > 0): ?>
                                <span style="color: var(--success-color);"><?php echo count($codes); ?> codes available</span>
                            <?php else: ?>
                                <span style="color: var(--danger-color);">No codes available</span>
                            <?php endif; ?>
                    </span>
                </div>
            </div>

            <h2>What to Do</h2>
            <div style="background: rgba(79, 195, 247, 0.1); border-left: 4px solid #4fc3f7; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <?php if (!$userData['two_fa_enabled']): ?>
                    <p style="color: #4fc3f7;">
                        <strong>2FA is currently DISABLED.</strong>
                    </p>
                    <p style="color: var(--text-secondary);">
                        To enable 2FA and have it prompt during login:
                    </p>
                    <ol style="color: var(--text-secondary);">
                        <li>Go to <a href="settings.php" style="color: #4fc3f7;">Settings</a></li>
                        <li>Click "Enable 2FA" button</li>
                        <li>Follow the setup wizard to scan the QR code</li>
                        <li>Save your backup codes in a safe place</li>
                        <li>After that, you'll be prompted for 2FA on every login</li>
                    </ol>
                <?php else: ?>
                    <p style="color: #20bf6b;">
                        <strong>✓ 2FA is ENABLED!</strong>
                    </p>
                    <p style="color: var(--text-secondary);">
                        Next time you login, you'll be prompted to enter your 2FA code after entering your password.
                    </p>
                    <p style="color: var(--text-secondary); margin-top: 15px;">
                        <a href="settings.php" style="color: #4fc3f7;">Go to Settings</a> to regenerate backup codes or manage your 2FA settings.
                    </p>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="settings.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                    ⚙️ Go to Settings
                </a>
                <a href="game.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; margin-left: 10px;">
                    🎮 Back to Game
                </a>
            </div>
        </div>
    </div>
</body>
</html>
