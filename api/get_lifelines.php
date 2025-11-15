<?php
// api/get_lifelines.php - Get current lifeline status
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

$session_id = $_GET['session_id'] ?? '';

if (empty($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing session ID']);
    exit();
}

try {
    global $database;
    
    // Get current lifeline counts
    $query = "SELECT add_time_remaining, fifty_fifty_remaining, skip_remaining 
              FROM lifelines 
              WHERE session_id = ?";
    $stmt = $database->executeQuery($query, [$session_id]);
    $lifelines = $database->fetchOne($stmt);
    
    if (!$lifelines) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'lifelines' => $lifelines
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching lifelines: ' . $e->getMessage()
    ]);
}
?>