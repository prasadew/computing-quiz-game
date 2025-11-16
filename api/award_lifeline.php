<?php
// api/award_lifeline.php - Award lifeline for winning banana game
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

if (empty($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing session ID']);
    exit();
}

try {
    global $database;
    
    // Check how many times banana game has been used for this session (allow up to 2)
    $checkQuery = "SELECT banana_used FROM lifelines WHERE session_id = ?";
    $stmt = $database->executeQuery($checkQuery, [$session_id]);
    $lifeline = $database->fetchOne($stmt);

    if (!$lifeline) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit();
    }

    // banana_used column originally stored as BOOLEAN; treat it as an integer counter here
    $uses = intval($lifeline['banana_used']);
    if ($uses >= 2) {
        echo json_encode(['success' => false, 'message' => 'Banana game already used maximum times']);
        exit();
    }

    // Award one lifeline (add to skip) and increment banana_used counter
    $updateQuery = "UPDATE lifelines 
                    SET skip_remaining = skip_remaining + 1,
                        banana_used = banana_used + 1
                    WHERE session_id = ?";
    $database->executeQuery($updateQuery, [$session_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Lifeline awarded successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error awarding lifeline: ' . $e->getMessage()
    ]);
}
?>