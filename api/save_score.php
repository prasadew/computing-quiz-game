<?php
// api/save_score.php - Save final game score
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

$auth = new Auth();

// Verify authentication
if (!$auth->isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$session_id = $input['session_id'] ?? '';
$score = $input['score'] ?? 0;
$difficulty = ucfirst(strtolower($input['difficulty'] ?? ''));
$questions_answered = $input['questions_answered'] ?? 0;

if (empty($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing session ID']);
    exit();
}

try {
    global $database;
    $user_id = $auth->getUserId();
    
    // End the game session
    $updateQuery = "UPDATE game_sessions 
                    SET is_active = 0, 
                        ended_at = NOW(),
                        current_score = ?,
                        questions_answered = ?
                    WHERE session_id = ? AND user_id = ?";
    $database->executeQuery($updateQuery, [$score, $questions_answered, $session_id, $user_id]);
    
    // Save score to scores table
    $scoreQuery = "INSERT INTO scores (user_id, score, difficulty, created_at)
                   VALUES (?, ?, ?, NOW())";
    $database->executeQuery($scoreQuery, [$user_id, $score, $difficulty]);
    
    // Update user's total score using stored procedure
    $procQuery = "CALL UpdateUserTotalScore(?, ?)";
    $database->executeQuery($procQuery, [$user_id, $score]);
    
    // Get updated total score
    $totalQuery = "SELECT total_score FROM users WHERE id = ?";
    $stmt = $database->executeQuery($totalQuery, [$user_id]);
    $user = $database->fetchOne($stmt);
    
    echo json_encode([
        'success' => true,
        'message' => 'Score saved successfully',
        'new_total_score' => $user['total_score']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving score: ' . $e->getMessage()
    ]);
}
?>