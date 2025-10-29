<?php
// Sample leaderboard data
$leaderboard = [
    [
        'name' => 'Emma Davis',
        'total_score' => 3000,
        'games_played' => 12,
        'highest_score' => 500
    ],
    [
        'name' => 'Alice Smith',
        'total_score' => 2100,
        'games_played' => 8,
        'highest_score' => 450
    ],
    [
        'name' => 'Bob Wilson',
        'total_score' => 1800,
        'games_played' => 6,
        'highest_score' => 350
    ],
    [
        'name' => 'John Doe',
        'total_score' => 1250,
        'games_played' => 5,
        'highest_score' => 300
    ],
    [
        'name' => 'Mike Johnson',
        'total_score' => 900,
        'games_played' => 3,
        'highest_score' => 400
    ]
];

// Sample user rank data
$userRank = [
    'user_rank' => 4,
    'total_score' => 1250,
    'name' => 'John Doe'
];

// Mock session for demo
$_SESSION['user_id'] = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Computing Quiz Game</title>
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <div class="container">
        <div class="game-header">
            <h1>🏆 Leaderboard 🏆</h1>
            <p style="position: relative; z-index: 1; font-size: 1.2em; margin-top: 10px;">
                Top Players in History of Computing Quiz
            </p>
        </div>

        <div class="card fade-in">
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'game.php' : 'index.php'; ?>" 
                   class="btn btn-primary" 
                   style="text-decoration: none; display: inline-block;">
                    <?php echo isset($_SESSION['user_id']) ? '🎮 Back to Game' : '🏠 Back to Home'; ?>
                </a>
            </div>

            <?php if (empty($leaderboard)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 5em; margin-bottom: 20px;">🎮</div>
                    <h2 style="color: var(--text-secondary); font-size: 1.5em;">
                        No scores yet! Be the first to play!
                    </h2>
                    <a href="register.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block; margin-top: 30px;">
                        🚀 Register & Play
                    </a>
                </div>
            <?php else: ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Rank</th>
                            <th>Player</th>
                            <th style="text-align: center;">Total Score</th>
                            <th style="text-align: center;">Games Played</th>
                            <th style="text-align: center;">Highest Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $index => $player): ?>
                            <tr>
                                <td>
                                    <span class="rank-badge <?php echo $index < 3 ? 'rank-' . ($index + 1) : ''; ?>" 
                                          style="<?php echo $index >= 3 ? 'background: var(--card-bg); color: var(--text-primary);' : ''; ?>">
                                        <?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="font-size: 1.2em; color: var(--glow-color);">
                                        <?php echo htmlspecialchars($player['name']); ?>
                                    </strong>
                                    <?php if ($index === 0): ?>
                                        <span style="margin-left: 10px; font-size: 1.5em;">👑</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; font-size: 1.3em; color: var(--accent-pink); font-weight: bold;">
                                    <?php echo number_format($player['total_score']); ?>
                                </td>
                                <td style="text-align: center; color: var(--text-secondary);">
                                    <?php echo $player['games_played'] ?? 0; ?>
                                </td>
                                <td style="text-align: center; color: var(--success-color); font-weight: bold;">
                                    <?php echo number_format($player['highest_score'] ?? 0); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="margin-top: 40px; padding-top: 30px; border-top: 3px solid var(--accent-purple); text-align: center;">
                    <?php
                    // Get current user's rank
                    $user_id = $_SESSION['user_id'];
                    $rankQuery = "SELECT 
                                  (SELECT COUNT(*) FROM users u2 WHERE u2.total_score > u1.total_score) + 1 as user_rank,
                                  u1.total_score,
                                  u1.name
                                  FROM users u1
                                  WHERE u1.id = ?";
                    $rankStmt = $database->executeQuery($rankQuery, [$user_id]);
                    $userRank = $database->fetchOne($rankStmt);
                    ?>
                    
                    <?php if ($userRank): ?>
                        <div style="background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink)); padding: 25px; border-radius: 15px;">
                            <h3 style="font-family: 'Bangers', cursive; font-size: 2em; margin-bottom: 15px;">
                                Your Ranking
                            </h3>
                            <p style="font-size: 1.5em; margin: 10px 0;">
                                Rank: <strong style="color: var(--glow-color); text-shadow: 0 0 10px var(--glow-color);">
                                    #<?php echo $userRank['user_rank']; ?>
                                </strong>
                            </p>
                            <p style="font-size: 1.3em;">
                                Total Score: <strong><?php echo number_format($userRank['total_score']); ?></strong>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 40px; text-align: center; padding: 30px; background: var(--card-bg); border-radius: 20px;">
            <h3 style="font-family: 'Bangers', cursive; font-size: 2em; color: var(--accent-pink); margin-bottom: 20px;">
                💡 Scoring System
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px; border: 2px solid var(--success-color);">
                    <h4 style="color: var(--success-color); margin-bottom: 10px;">Easy</h4>
                    <p style="font-size: 1.5em; color: var(--text-primary);">10 points</p>
                </div>
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px; border: 2px solid var(--warning-color);">
                    <h4 style="color: var(--warning-color); margin-bottom: 10px;">Medium</h4>
                    <p style="font-size: 1.5em; color: var(--text-primary);">20 points</p>
                </div>
                <div style="padding: 20px; background: var(--darker-bg); border-radius: 15px; border: 2px solid var(--danger-color);">
                    <h4 style="color: var(--danger-color); margin-bottom: 10px;">Hard</h4>
                    <p style="font-size: 1.5em; color: var(--text-primary);">30 points</p>
                </div>
            </div>
            <p style="margin-top: 20px; color: var(--glow-color); font-size: 1.1em;">
                ⚡ Bonus: +5-10 points for quick answers!
            </p>
        </div>
    </div>

    <script>
        // Add animation to table rows
        document.querySelectorAll('.leaderboard-table tbody tr').forEach((row, index) => {
            row.style.animation = `slideIn 0.5s ease-out ${index * 0.1}s forwards`;
            row.style.opacity = '0';
        });
    </script>
</body>
</html>