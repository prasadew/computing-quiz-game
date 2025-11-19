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

    // Convert difficulty to proper case to match ENUM values
    $difficulty = ucfirst(strtolower($difficulty));

    // If a session with this session_id already exists for this user, return success (idempotent)
    $checkQuery = "SELECT id FROM game_sessions WHERE session_id = ? AND user_id = ?";
    $stmtCheck = $database->executeQuery($checkQuery, [$session_id, $user_id]);
    $existing = $database->fetchOne($stmtCheck);

    if ($existing) {
        // Ensure lifelines row exists for this session_id (avoid duplicate lifelines)
        $lifelineCheck = "SELECT id FROM lifelines WHERE session_id = ? AND user_id = ?";
        $lfStmt = $database->executeQuery($lifelineCheck, [$session_id, $user_id]);
        $lfExisting = $database->fetchOne($lfStmt);
        if (!$lfExisting) {
            $lifelineInsert = "INSERT INTO lifelines (user_id, session_id, add_time_remaining, fifty_fifty_remaining, skip_remaining, banana_used, created_at)
                               VALUES (?, ?, 3, 3, 3, 0, NOW())";
            $database->executeQuery($lifelineInsert, [$user_id, $session_id]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Session already initialized'
        ]);
        exit();
    }

    // Create game session
    $query = "INSERT INTO game_sessions (user_id, session_id, difficulty, current_score, questions_answered, is_active, started_at)
              VALUES (?, ?, ?, 0, 0, 1, NOW())";
    $database->executeQuery($query, [$user_id, $session_id, $difficulty]);

    // Initialize lifelines for this session
    $lifelineQuery = "INSERT INTO lifelines (user_id, session_id, add_time_remaining, fifty_fifty_remaining, skip_remaining, banana_used, created_at)
                      VALUES (?, ?, 3, 3, 3, 0, NOW())";
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