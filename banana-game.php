<?php
// banana-game.php - Banana Pattern Game
session_start();
require_once 'includes/auth.php';

$auth = new Auth();
$auth->requireAuth();

$session_id = $_GET['session_id'] ?? '';
if (empty($session_id)) {
    header('Location: game.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana Game - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        .banana-game-container {
            text-align: center;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .pattern-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        .answer-input {
            font-size: 1.2em;
            padding: 10px;
            width: 100px;
            text-align: center;
            margin: 10px;
            background: var(--darker-bg);
            color: var(--text-primary);
            border: 2px solid var(--accent-purple);
            border-radius: 5px;
        }
        .answer-input:focus {
            border-color: var(--glow-color);
            outline: none;
            box-shadow: 0 0 10px var(--glow-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🍌 Banana Pattern Game</h1>
            <p>Solve the pattern to restore one lifeline!</p>
        </div>

        <div class="card fade-in banana-game-container" id="bananaGameContainer">
            <p>Find the missing number in the pattern:</p>
            <img id="bananaImage" class="pattern-image" src="" alt="Banana Game Pattern" style="display:none;" />
            <div id="loading" style="margin: 20px 0; font-size: 1.2em;">Loading pattern...</div>
            <div id="answerSection" style="display:none;">
                <input type="number" id="answerInput" class="answer-input" min="0" max="9" placeholder="#" required>
                <button onclick="submitBananaAnswer()" class="btn btn-primary">Submit Answer</button>
            </div>
            <div id="result" style="margin: 20px 0; font-size: 1.2em;"></div>
            <button onclick="window.location.href='game.php'" class="btn btn-secondary">Back to Game</button>
        </div>
    </div>

    <script>
        let bananaSolution = null;
        // Fetch banana game question from API
        async function loadBananaGame() {
            try {
                const response = await fetch('https://marcconrad.com/uob/banana/api.php');
                const data = await response.json();
                if (data && data.question && data.solution !== undefined) {
                    document.getElementById('bananaImage').src = data.question;
                    document.getElementById('bananaImage').style.display = 'block';
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('answerSection').style.display = 'block';
                    bananaSolution = data.solution;
                } else {
                    document.getElementById('loading').textContent = 'Error loading pattern. Please refresh.';
                }
            } catch (err) {
                document.getElementById('loading').textContent = 'Error loading pattern. Please check your connection.';
            }
        }

        // Submit answer and check against solution
        async function submitBananaAnswer() {
            const answer = document.getElementById('answerInput').value;
            const resultDiv = document.getElementById('result');
            if (bananaSolution === null) {
                resultDiv.innerHTML = '❌ Error: No solution loaded.';
                return;
            }
            if (parseInt(answer) === bananaSolution) {
                resultDiv.innerHTML = '✅ Correct! Restoring a lifeline...';
                // Call API to restore lifeline
                try {
                    const response = await fetch('api/award_lifeline.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            session_id: '<?php echo htmlspecialchars($session_id); ?>'
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        sessionStorage.setItem('bananaGameSuccess', 'true');
                        window.location.href = 'game.php';
                    } else {
                        resultDiv.innerHTML = '❌ Error: ' + data.message;
                    }
                } catch (error) {
                    resultDiv.innerHTML = '❌ Error updating lifeline. Please try again.';
                }
            } else {
                resultDiv.innerHTML = '❌ Wrong answer. Try again!';
            }
        }

        // Load banana game on page load
        window.addEventListener('DOMContentLoaded', loadBananaGame);
        // Allow Enter key to submit answer
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('answerInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    submitBananaAnswer();
                }
            });
        });
    </script>
</body>
</html>