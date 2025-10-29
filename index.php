<?php
// index.php - Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History of Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🖥️ History of Computing Quiz 🎮</h1>
            <p style="font-size: 1.3em; margin-top: 15px; position: relative; z-index: 1;">
                Test your knowledge about the pioneers and milestones of computing!
            </p>
        </div>

        <div class="card fade-in" style="max-width: 600px; margin: 0 auto;">
            <div class="text-center">
                <h2 style="font-family: 'Bangers', cursive; font-size: 2.5em; color: var(--glow-color); text-shadow: 0 0 15px var(--glow-color); margin-bottom: 30px;">
                    Welcome, Player!
                </h2>
                
                <div style="margin: 40px 0; padding: 30px; background: var(--darker-bg); border-radius: 15px; border: 3px solid var(--accent-purple);">
                    <h3 style="color: var(--accent-pink); margin-bottom: 20px; font-size: 1.5em;">🎯 Game Features</h3>
                    <ul style="list-style: none; text-align: left; font-size: 1.1em; line-height: 2;">
                        <li>✅ Three difficulty levels</li>
                        <li>⏰ Timed questions with countdown</li>
                        <li>🎁 Three unique lifelines per game</li>
                        <li>🍌 Bonus Banana mini-game</li>
                        <li>🏆 Global leaderboard</li>
                        <li>💯 Score tracking system</li>
                    </ul>
                </div>

                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                    <a href="login.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                        🎮 Login to Play
                    </a>
                    <a href="register.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">
                        📝 Register Now
                    </a>
                </div>

                <div style="margin-top: 30px;">
                    <a href="leaderboard.php" style="color: var(--glow-color); text-decoration: none; font-size: 1.2em;">
                        🏆 View Leaderboard
                    </a>
                </div>
            </div>
        </div>

        <div style="margin-top: 50px; text-align: center; padding: 30px; background: var(--card-bg); border-radius: 20px;">
            <h3 style="font-family: 'Bangers', cursive; font-size: 2em; color: var(--accent-pink); margin-bottom: 20px;">
                🎲 How to Play
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px;">
                    <div style="font-size: 3em; margin-bottom: 10px;">1️⃣</div>
                    <h4 style="color: var(--glow-color); margin-bottom: 10px;">Choose Difficulty</h4>
                    <p style="color: var(--text-secondary);">Select Easy, Medium, or Hard mode</p>
                </div>
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px;">
                    <div style="font-size: 3em; margin-bottom: 10px;">2️⃣</div>
                    <h4 style="color: var(--glow-color); margin-bottom: 10px;">Answer Questions</h4>
                    <p style="color: var(--text-secondary);">Beat the timer and choose correctly</p>
                </div>
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px;">
                    <div style="font-size: 3em; margin-bottom: 10px;">3️⃣</div>
                    <h4 style="color: var(--glow-color); margin-bottom: 10px;">Use Lifelines</h4>
                    <p style="color: var(--text-secondary);">Add time, 50/50, or skip questions</p>
                </div>
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px;">
                    <div style="font-size: 3em; margin-bottom: 10px;">4️⃣</div>
                    <h4 style="color: var(--glow-color); margin-bottom: 10px;">Climb the Ranks</h4>
                    <p style="color: var(--text-secondary);">Compete for the top score!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add floating animation to cards
        document.querySelectorAll('.card').forEach((card, index) => {
            card.style.animation = `fadeIn 0.5s ease-in ${index * 0.2}s forwards`;
            card.style.opacity = '0';
        });
    </script>
</body>
</html>