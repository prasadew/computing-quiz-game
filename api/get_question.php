<?php
// api/get_question.php - Fetch random question based on difficulty
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

$difficulty = $_GET['difficulty'] ?? '';
$session_id = $_GET['session_id'] ?? '';

if (empty($difficulty) || empty($session_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    global $database;
    
    // Get a random question that hasn't been asked in this session
    // Convert difficulty to proper case to match ENUM values
    $difficulty = ucfirst(strtolower($difficulty));
    
    $query = "SELECT q.* 
              FROM questions q
              WHERE q.difficulty = ?
              AND q.id NOT IN (
                  SELECT question_id 
                  FROM session_questions 
                  WHERE session_id = ?
              )
              ORDER BY RAND()
              LIMIT 1";
    
    error_log("Executing query for difficulty: " . $difficulty . ", session: " . $session_id);
    $stmt = $database->executeQuery($query, [$difficulty, $session_id]);
    $question = $database->fetchOne($stmt);
    
    if (!$question) {
        error_log("No question found for difficulty: " . $difficulty);
    } else {
        error_log("Found question ID: " . $question['id']);
    }
    
    if (!$question) {
        echo json_encode([
            'success' => false,
            'message' => 'No more questions available'
        ]);
        exit();
    }
    
    // Mark question as asked in this session
    $insertQuery = "INSERT INTO session_questions (session_id, question_id, asked_at) 
                    VALUES (?, ?, NOW())";
    $database->executeQuery($insertQuery, [$session_id, $question['id']]);
    
    echo json_encode([
        'success' => true,
        'question' => $question
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching question: ' . $e->getMessage()
    ]);
}
?>