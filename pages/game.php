<?php
// game.php - Main Game Interface
session_start();
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireAuth();

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Quiz - Computing Quiz Game</title>
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🖥️ History of Computing Quiz 🎮</h1>
            <p style="position: relative; z-index: 1; font-size: 1.2em; margin-top: 10px;">
                Welcome, <strong><?php echo htmlspecialchars($user['name']); ?></strong>! 
                Total Score: <span id="totalScore"><?php echo $user['total_score']; ?></span>
            </p>
            <div style="position: relative; z-index: 1; margin-top: 15px;">
                <a href="../pages/leaderboard.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; margin-right: 10px;">
                    🏆 Leaderboard
                </a>
                <a href="../setup/setup-guide.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; margin-right: 10px;">
                    🔐 Setup 2FA
                </a>
                <a href="../pages/settings.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; margin-right: 10px;">
                    ⚙️ Settings
                </a>
                <a href="../auth/logout.php" class="btn btn-warning" style="text-decoration: none; display: inline-block;">
                    🚪 Logout
                </a>
            </div>
        </div>

        <!-- Difficulty Selection Screen -->
        <div id="difficultyScreen" class="card fade-in">
            <h2 class="text-center" style="font-family: 'Bangers', cursive; font-size: 2.5em; color: var(--glow-color); text-shadow: 0 0 15px var(--glow-color); margin-bottom: 40px;">
                Choose Your Difficulty Level
            </h2>

            <div class="difficulty-grid">
                <div class="difficulty-card easy">
                    <div style="font-size: 4em; margin-bottom: 15px;">😊</div>
                    <h3 style="color: var(--success-color);">EASY</h3>
                    <p style="color: var(--text-secondary); font-size: 1.1em; margin-top: 15px;">
                        Perfect for beginners<br>
                        30 seconds per question
                    </p>
                </div>

                <div class="difficulty-card medium">
                    <div style="font-size: 4em; margin-bottom: 15px;">🤔</div>
                    <h3 style="color: var(--warning-color);">MEDIUM</h3>
                    <p style="color: var(--text-secondary); font-size: 1.1em; margin-top: 15px;">
                        For tech enthusiasts<br>
                        20 seconds per question
                    </p>
                </div>

                <div class="difficulty-card hard">
                    <div style="font-size: 4em; margin-bottom: 15px;">😤</div>
                    <h3 style="color: var(--danger-color);">HARD</h3>
                    <p style="color: var(--text-secondary); font-size: 1.1em; margin-top: 15px;">
                        For computing experts<br>
                        15 seconds per question
                    </p>
                </div>
            </div>
        </div>

        <!-- Game Screen -->
        <div id="gameScreen" class="hidden">
            <!-- Score and Timer Display -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                <div class="score-display">
                    Score: <span id="currentScore">0</span>
                </div>
                <div class="timer-container">
                    <div class="timer" id="timer">20</div>
                </div>
            </div>

            <!-- Lifelines -->
            <div class="lifelines-container">
                <button class="lifeline-btn" id="addTimeBtn" onclick="useLifeline('addTime')">
                    ⏰ Add 10s
                    <span class="count" id="addTimeCount">3</span>
                </button>
                <button class="lifeline-btn" id="fiftyFiftyBtn" onclick="useLifeline('fiftyFifty')">
                    ↔️ 50/50
                    <span class="count" id="fiftyFiftyCount">3</span>
                </button>
                <button class="lifeline-btn" id="skipBtn" onclick="useLifeline('skip')">
                    ⏭️ Skip
                    <span class="count" id="skipCount">3</span>
                </button>
            </div>

            <!-- Question Card -->
            <div class="question-card">
                <div class="question-text" id="questionText">
                    Loading question...
                </div>

                <div class="options-grid" id="optionsGrid">
                    <!-- Options will be loaded dynamically -->
                </div>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div id="gameOverModal" class="modal">
            <div class="modal-content">
                <h2 id="gameOverTitle">🎮 Game Over!</h2>
                <div style="margin: 30px 0;">
                    <p style="font-size: 2em; color: var(--glow-color); text-shadow: 0 0 15px var(--glow-color);">
                        Final Score: <strong id="finalScore">0</strong>
                    </p>
                    <p style="font-size: 1.3em; margin-top: 15px; color: var(--text-secondary);">
                        Questions Answered: <span id="questionsAnswered">0</span>
                    </p>
                </div>

                <div id="bananaGameOption" class="hidden" style="background: var(--darker-bg); padding: 20px; border-radius: 15px; margin: 20px 0; border: 3px solid var(--warning-color);">
                    <p style="font-size: 1.2em; margin-bottom: 15px;">
                        🍌 Out of lifelines? Play the Banana Game to earn one more!
                    </p>
                    <button class="btn btn-warning" onclick="playBananaGame()">
                        🎲 Play Banana Game
                    </button>
                </div>

                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="location.reload()">
                        🔄 Play Again
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='../pages/leaderboard.php'">
                        🏆 View Leaderboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Elements -->
    <audio id="correctSound" preload="auto">
        <source src="../assets/sounds/correct.mp3" type="audio/mpeg">
    </audio>
    <audio id="wrongSound" preload="auto">
        <source src="../assets/sounds/wrong.mp3" type="audio/mpeg">
    </audio>
    <audio id="tickSound" preload="auto">
        <source src="../assets/sounds/tick.mp3" type="audio/mpeg">
    </audio>
    <audio id="gameOverSound" preload="auto">
        <source src="../assets/sounds/gameover.mp3" type="audio/mpeg">
    </audio>

    <script src="../assets/js/game.js"></script>
</body>
</html>