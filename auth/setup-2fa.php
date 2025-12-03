<?php
// setup-2fa.php - Setup Two-Factor Authentication
session_start();
require_once '../includes/auth.php';
require_once '../includes/two-factor-auth.php';
require_once '../config/database.php';

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
$step = $_GET['step'] ?? 'show'; // show, confirm, complete
$qrCodeUrl = '';
$backupCodes = [];

// Get existing 2FA status
$query = "SELECT two_fa_enabled FROM users WHERE id = ?";
$stmt = $database->executeQuery($query, [$user['id']]);
$userData = $database->fetchOne($stmt);
$twoFAEnabled = $userData['two_fa_enabled'] ?? false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'generate') {
            // Generate new secret and backup codes
            $secret = $twoFA->generateSecret();
            $codes = $twoFA->generateBackupCodes();
            
            // Store in session temporarily
            $_SESSION['2fa_setup_secret'] = $secret;
            $_SESSION['2fa_setup_codes'] = $codes;
            
            $qrCodeUrl = $twoFA->getQRCodeURL($user['email'], $secret);
            $backupCodes = $codes;
            $step = 'confirm';
            
        } elseif ($_POST['action'] === 'confirm') {
            // Verify the code entered by user
            $code = $_POST['code'] ?? '';
            $secret = $_SESSION['2fa_setup_secret'] ?? '';
            $codes = $_SESSION['2fa_setup_codes'] ?? [];
            
            if (empty($secret) || empty($codes)) {
                $error = 'Setup session expired. Please start again.';
                $step = 'show';
            } elseif ($twoFA->verifyCode($secret, $code)) {
                // Save to database
                $query = "UPDATE users SET two_fa_enabled = 1, two_fa_secret = ?, two_fa_backup_codes = ? WHERE id = ?";
                $codesJSON = json_encode($codes);
                $database->executeQuery($query, [$secret, $codesJSON, $user['id']]);
                
                // Clear session
                unset($_SESSION['2fa_setup_secret']);
                unset($_SESSION['2fa_setup_codes']);
                
                $step = 'complete';
                $success = '2FA has been successfully enabled!';
                $backupCodes = $codes;
            } else {
                $error = 'Invalid code. Please try again.';
                $qrCodeUrl = $twoFA->getQRCodeURL($_SESSION['2fa_setup_secret'] ?? '', $_SESSION['2fa_setup_secret'] ?? '');
                $backupCodes = $_SESSION['2fa_setup_codes'] ?? [];
                $step = 'confirm';
            }
        }
    }
}

// For display purposes when showing existing setup
if ($step === 'show' && isset($_SESSION['2fa_setup_secret'])) {
    $qrCodeUrl = $twoFA->getQRCodeURL($user['email'], $_SESSION['2fa_setup_secret']);
    $backupCodes = $_SESSION['2fa_setup_codes'] ?? [];
    $step = 'confirm';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Two-Factor Authentication - Computing Quiz Game</title>
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
    <style>
        .qr-container {
            text-align: center;
            margin: 30px 0;
        }

        .qr-container img {
            max-width: 300px;
            border-radius: 10px;
            border: 3px solid var(--accent-purple);
            background: white;
            padding: 10px;
        }

        .backup-codes {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid var(--glow-color);
        }

        .backup-codes h3 {
            color: var(--glow-color);
            margin-top: 0;
        }

        .code-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }

        .code-item {
            background: var(--bg-primary);
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.9em;
            user-select: all;
            cursor: pointer;
            border: 1px solid var(--accent-purple);
        }

        .code-item:hover {
            background: var(--accent-purple);
            opacity: 0.8;
        }

        .warning {
            background: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            color: #ffc107;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-secondary);
            border: 2px solid var(--accent-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: var(--accent-purple);
        }

        .step.active .step-number {
            background: var(--glow-color);
            color: white;
        }

        .step.completed .step-number {
            background: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        .step-label {
            font-size: 0.9em;
            color: var(--text-secondary);
        }

        .step.active .step-label {
            color: var(--glow-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🔐 Two-Factor Authentication Setup</h1>
        </div>

        <div class="card fade-in" style="max-width: 600px; margin: 0 auto;">
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

            <!-- Step Indicator -->
            <div class="steps">
                <div class="step <?php echo $step !== 'show' ? 'active' : ''; ?> <?php echo in_array($step, ['confirm', 'complete']) ? 'completed' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">Generate</div>
                </div>
                <div class="step <?php echo $step === 'confirm' ? 'active' : ''; ?> <?php echo $step === 'complete' ? 'completed' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">Verify</div>
                </div>
                <div class="step <?php echo $step === 'complete' ? 'active completed' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>

            <!-- Step 1: Generate Setup -->
            <?php if ($step === 'show'): ?>
                <div style="text-align: center;">
                    <p style="color: var(--text-secondary); margin: 20px 0;">
                        Enable two-factor authentication to add an extra layer of security to your account.
                    </p>
                    
                    <div class="warning">
                        <strong>⚠️ Important:</strong> You'll need an authenticator app like Google Authenticator, Microsoft Authenticator, or Authy to proceed.
                    </div>

                    <form method="POST" action="setup-2fa.php">
                        <input type="hidden" name="action" value="generate">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            📱 Download Authenticator App & Continue
                        </button>
                    </form>
                </div>

            <!-- Step 2: Confirm Setup -->
            <?php elseif ($step === 'confirm'): ?>
                <div>
                    <h3 style="text-align: center; color: var(--glow-color);">Step 1: Scan QR Code</h3>
                    <p style="color: var(--text-secondary); text-align: center;">
                        Open your authenticator app and scan this QR code:
                    </p>

                    <?php if ($qrCodeUrl): ?>
                        <div class="qr-container">
                            <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="2FA QR Code">
                        </div>
                        <p style="color: var(--text-secondary); text-align: center; font-size: 0.9em;">
                            Can't scan? Enter this code manually: <code><?php echo htmlspecialchars($_SESSION['2fa_setup_secret']); ?></code>
                        </p>
                    <?php endif; ?>

                    <h3 style="text-align: center; color: var(--glow-color); margin-top: 30px;">Step 2: Verify Setup</h3>
                    <p style="color: var(--text-secondary); text-align: center;">
                        Enter the 6-digit code from your authenticator app:
                    </p>

                    <form method="POST" action="setup-2fa.php">
                        <div class="form-group">
                            <input
                                type="text"
                                name="code"
                                class="form-control"
                                style="letter-spacing: 15px; font-size: 2em; text-align: center; font-family: monospace;"
                                placeholder="000000"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                autocomplete="off"
                                autofocus
                            >
                        </div>
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            ✓ Verify Code
                        </button>
                    </form>

                    <h3 style="text-align: center; color: var(--glow-color); margin-top: 30px;">Step 3: Save Backup Codes</h3>
                    <p style="color: var(--text-secondary); text-align: center;">
                        Save these backup codes in a secure place. You can use them to access your account if you lose access to your authenticator app:
                    </p>

                    <?php if (!empty($backupCodes)): ?>
                        <div class="backup-codes">
                            <div class="code-list">
                                <?php foreach ($backupCodes as $code): ?>
                                    <div class="code-item" title="Click to copy"><?php echo htmlspecialchars($code); ?></div>
                                <?php endforeach; ?>
                            </div>
                            <p style="font-size: 0.9em; margin-bottom: 0;">
                                💡 Tip: Click on any code to select it for copying
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

            <!-- Step 3: Complete -->
            <?php elseif ($step === 'complete'): ?>
                <div style="text-align: center;">
                    <div style="font-size: 3em; margin: 20px 0;">✅</div>
                    <h2 style="color: var(--success-color); margin: 20px 0;">Two-Factor Authentication Enabled!</h2>
                    
                    <p style="color: var(--text-secondary); margin: 20px 0;">
                        Your account is now protected with 2FA. You'll need to enter a code from your authenticator app each time you log in.
                    </p>

                    <?php if (!empty($backupCodes)): ?>
                        <div class="backup-codes">
                            <h3 style="margin-top: 0;">📋 Save Your Backup Codes</h3>
                            <p style="color: var(--text-secondary);">
                                These codes are your backup access methods. Store them securely!
                            </p>
                            <div class="code-list">
                                <?php foreach ($backupCodes as $code): ?>
                                    <div class="code-item"><?php echo htmlspecialchars($code); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="../pages/game.php" class="btn btn-primary" style="text-decoration: none; display: inline-block; margin-top: 30px;">
                        🎮 Return to Game
                    </a>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--accent-purple);">
                <a href="../pages/game.php" style="color: var(--glow-color); text-decoration: none;">
                    ⬅️ Back to Game
                </a>
            </div>
        </div>
    </div>

    <script>
        // Allow digits only
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput && codeInput.maxLength === 6) {
            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Copy on click for backup codes
        document.querySelectorAll('.code-item').forEach(item => {
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
    </script>
</body>
</html>
