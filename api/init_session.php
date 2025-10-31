<?php
// api/init_session.php - Initialize new game session
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
$difficulty = $input['difficulty'] ?? '';

if (empty($session_id) || empty($difficulty)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    global $database;
    $user_id = $auth->getUserId();
    
    // Create game session
    $query = "INSERT INTO game_sessions (user_id, session_id, difficulty, current_score, questions_answered, is_active, started_at)
              VALUES (?, ?, ?, 0, 0, 1, GETDATE())";
    $database->executeQuery($query, [$user_id, $session_id, $difficulty]);
    
    // Initialize lifelines for this session
    $lifelineQuery = "INSERT INTO lifelines (user_id, session_id, add_time_remaining, fifty_fifty_remaining, skip_remaining, banana_used, created_at)
                      VALUES (?, ?, 3, 3, 3, 0, GETDATE())";
    $database->executeQuery($lifelineQuery, [$user_id, $session_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Session initialized successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error initializing session: ' . $e->getMessage()
    ]);
}
?>