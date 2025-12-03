<?php
// api/submit_answer.php - Handle answer submission
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
$question_id = $input['question_id'] ?? '';
$selected_option = $input['selected_option'] ?? '';
$time_taken = $input['time_taken'] ?? 0;

if (empty($session_id) || empty($question_id) || empty($selected_option)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    global $database;

    if (strpos($question_id, 'api_') === 0) {
        // API question: validate using frontend data
        $correct_option = $input['correct_option'] ?? '';
        $difficulty = $input['difficulty'] ?? 'Easy';
        $is_correct = (strtoupper($selected_option) === strtoupper($correct_option));

        // Calculate points based on difficulty and time
        $points_earned = 0;
        if ($is_correct) {
            switch($difficulty) {
                case 'Easy':
                    $points_earned = 10;
                    break;
                case 'Medium':
                    $points_earned = 20;
                    break;
                case 'Hard':
                    $points_earned = 30;
                    break;
            }
            if ($time_taken <= 5) {
                $points_earned += 10;
            } elseif ($time_taken <= 10) {
                $points_earned += 5;
            }
        }

        // Optionally, you can record API answers in a separate table or skip DB write

        // Update session score
        $updateQuery = "UPDATE game_sessions 
                        SET current_score = current_score + ?,
                            questions_answered = questions_answered + 1
                        WHERE session_id = ?";
        $database->executeQuery($updateQuery, [$points_earned, $session_id]);

        echo json_encode([
            'success' => true,
            'is_correct' => $is_correct,
            'correct_option' => $correct_option,
            'points_earned' => $points_earned
        ]);
        exit();
    }

    // Local DB question (original logic)
    // Get correct answer
    $query = "SELECT correct_option, difficulty FROM questions WHERE id = ?";
    $stmt = $database->executeQuery($query, [$question_id]);
    $question = $database->fetchOne($stmt);

    if (!$question) {
        echo json_encode(['success' => false, 'message' => 'Question not found']);
        exit();
    }

    $is_correct = (strtoupper($selected_option) === strtoupper($question['correct_option']));

    // Calculate points based on difficulty and time
    $points_earned = 0;
    if ($is_correct) {
        switch($question['difficulty']) {
            case 'Easy':
                $points_earned = 10;
                break;
            case 'Medium':
                $points_earned = 20;
                break;
            case 'Hard':
                $points_earned = 30;
                break;
        }
        // Bonus points for quick answers
        if ($time_taken <= 5) {
            $points_earned += 10;
        } elseif ($time_taken <= 10) {
            $points_earned += 5;
        }
    }

    // Record the answer
    $recordQuery = "INSERT INTO session_answers (session_id, question_id, selected_option, is_correct, points_earned, time_taken, answered_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $database->executeQuery($recordQuery, [
        $session_id,
        $question_id,
        $selected_option,
        $is_correct ? 1 : 0,
        $points_earned,
        $time_taken
    ]);

    // Update session score
    $updateQuery = "UPDATE game_sessions 
                    SET current_score = current_score + ?,
                        questions_answered = questions_answered + 1
                    WHERE session_id = ?";
    $database->executeQuery($updateQuery, [$points_earned, $session_id]);

    echo json_encode([
        'success' => true,
        'is_correct' => $is_correct,
        'correct_option' => $question['correct_option'],
        'points_earned' => $points_earned
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting answer: ' . $e->getMessage()
    ]);
}
?>