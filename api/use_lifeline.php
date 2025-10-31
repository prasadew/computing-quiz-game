<?php
// api/use_lifeline.php - Handle lifeline usage
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
$lifeline_type = $input['lifeline_type'] ?? '';

if (empty($session_id) || empty($lifeline_type)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    global $database;
    
    // Map lifeline type to database column
    $columnMap = [
        'addTime' => 'add_time_remaining',
        'fiftyFifty' => 'fifty_fifty_remaining',
        'skip' => 'skip_remaining'
    ];
    
    if (!isset($columnMap[$lifeline_type])) {
        echo json_encode(['success' => false, 'message' => 'Invalid lifeline type']);
        exit();
    }
    
    $column = $columnMap[$lifeline_type];
    
    // Decrease lifeline count
    $query = "UPDATE lifelines 
              SET $column = $column - 1 
              WHERE session_id = ? AND $column > 0";
    $database->executeQuery($query, [$session_id]);
    
    // Get remaining lifelines
    $selectQuery = "SELECT add_time_remaining, fifty_fifty_remaining, skip_remaining 
                    FROM lifelines 
                    WHERE session_id = ?";
    $stmt = $database->executeQuery($selectQuery, [$session_id]);
    $lifelines = $database->fetchOne($stmt);
    
    echo json_encode([
        'success' => true,
        'message' => 'Lifeline used successfully',
        'remaining_lifelines' => $lifelines
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error using lifeline: ' . $e->getMessage()
    ]);
}
?>