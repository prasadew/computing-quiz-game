<?php
// setup-guide.php - 2FA Setup Guide
session_start();
require_once '../includes/auth.php';

$auth = new Auth();

// Require authentication
$auth->requireAuth();

$user = $auth->getCurrentUser();
if (!$user) {
    header('Location: ../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Setup Guide - Computing Quiz Game</title>
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
    <style>
        .step-card {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid var(--accent-purple);
        }

        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--glow-color);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            margin-right: 15px;
        }

        .step-title {
            color: var(--glow-color);
            font-size: 1.3em;
            margin: 15px 0 10px 0;
        }

        .app-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .app-item {
            background: var(--bg-primary);
            padding: 15px;
            border-radius: 5px;
            border: 1px solid var(--accent-purple);
            text-align: center;
        }

        .app-item a {
            color: var(--glow-color);
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .app-item a:hover {
            text-decoration: underline;
        }

        .info-box {
            background: rgba(79, 195, 247, 0.1);
            border-left: 4px solid #4fc3f7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #4fc3f7;
        }

        .warning-box {
            background: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🔐 Two-Factor Authentication Setup Guide</h1>
            <p style="color: var(--text-secondary); margin-top: 10px;">
                Follow these simple steps to enable 2FA on your account
            </p>
        </div>

        <div class="card fade-in" style="max-width: 800px; margin: 0 auto;">
            <div class="info-box">
                <strong>ℹ️ What is 2FA?</strong><br>
                Two-Factor Authentication adds an extra layer of security to your account. After entering your password, you'll need to provide a code from your authenticator app to complete the login.
            </div>

            <div class="step-card">
                <h2><span class="step-number">1</span> Download an Authenticator App</h2>
                <p style="color: var(--text-secondary);">
                    You'll need one of these apps on your phone:
                </p>
                <div class="app-list">
                    <div class="app-item">
                        <strong>Google Authenticator</strong>
                        <p style="font-size: 0.9em; color: var(--text-secondary); margin: 10px 0;">
                            The most popular choice
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
                            Android
                        </a>
                        <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank">
                            iOS
                        </a>
                    </div>

                    <div class="app-item">
                        <strong>Microsoft Authenticator</strong>
                        <p style="font-size: 0.9em; color: var(--text-secondary); margin: 10px 0;">
                            Microsoft's option
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank">
                            Android
                        </a>
                        <a href="https://apps.apple.com/app/microsoft-authenticator/id983156458" target="_blank">
                            iOS
                        </a>
                    </div>

                    <div class="app-item">
                        <strong>Authy</strong>
                        <p style="font-size: 0.9em; color: var(--text-secondary); margin: 10px 0;">
                            Feature-rich option
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank">
                            Android
                        </a>
                        <a href="https://apps.apple.com/app/authy/id494868841" target="_blank">
                            iOS
                        </a>
                    </div>

                    <div class="app-item">
                        <strong>FreeOTP</strong>
                        <p style="font-size: 0.9em; color: var(--text-secondary); margin: 10px 0;">
                            Open source option
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=org.fedorahosted.freeotp" target="_blank">
                            Android
                        </a>
                        <a href="https://apps.apple.com/app/freeotp/id872559395" target="_blank">
                            iOS
                        </a>
                    </div>
                </div>
            </div>

            <div class="step-card">
                <h2><span class="step-number">2</span> Go to Settings</h2>
                <p style="color: var(--text-secondary);">
                    Open your game account and navigate to the Settings page. You can do this from the game menu.
                </p>
            </div>

            <div class="step-card">
                <h2><span class="step-number">3</span> Enable 2FA</h2>
                <p style="color: var(--text-secondary);">
                    In the Settings page, find the "Two-Factor Authentication" section and click the "Enable 2FA" button.
                </p>
            </div>

            <div class="step-card">
                <h2><span class="step-number">4</span> Scan the QR Code</h2>
                <p style="color: var(--text-secondary);">
                    Open your authenticator app and scan the QR code displayed on the screen. If you can't scan, you can manually enter the provided code.
                </p>
            </div>

            <div class="step-card">
                <h2><span class="step-number">5</span> Verify Your Setup</h2>
                <p style="color: var(--text-secondary);">
                    Enter the 6-digit code from your authenticator app to confirm the setup is working correctly.
                </p>
            </div>

            <div class="step-card">
                <h2><span class="step-number">6</span> Save Your Backup Codes</h2>
                <p style="color: var(--text-secondary);">
                    You'll be given 8 backup codes. Save these in a secure place (like a password manager). You can use them to access your account if you lose your phone.
                </p>
                <div class="warning-box">
                    <strong>⚠️ Important:</strong> Store these backup codes securely! Without them and your phone, you may lose access to your account.
                </div>
            </div>

            <div class="step-card">
                <h2><span class="step-number">7</span> Start Using 2FA</h2>
                <p style="color: var(--text-secondary);">
                    The next time you log in, you'll be asked for:
                </p>
                <ol style="color: var(--text-secondary); margin-left: 20px;">
                    <li>Your email address</li>
                    <li>Your password</li>
                    <li>A 6-digit code from your authenticator app</li>
                </ol>
            </div>

            <div class="info-box">
                <strong>💡 Pro Tips:</strong><br>
                • Your authenticator code changes every 30 seconds<br>
                • You have about 30 seconds to enter the code before it expires<br>
                • If a code fails, wait for the next one to appear<br>
                • Each backup code can only be used once
            </div>

            <div style="text-align: center; margin-top: 30px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="../pages/settings.php" class="btn btn-primary" style="text-decoration: none;">
                    ⚙️ Go to Settings & Enable 2FA
                </a>
                <a href="../pages/game.php" class="btn btn-secondary" style="text-decoration: none;">
                    🎮 Back to Game
                </a>
            </div>
        </div>
    </div>
</body>
</html>
