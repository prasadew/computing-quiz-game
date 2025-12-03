<?php
// banana-game-access.php
// Helper to check if user can access banana game
require_once __DIR__ . '/config/database.php';

function canAccessBananaGame($session_id) {
    // Example: check if session has completed all questions
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT questions_answered, total_questions FROM sessions WHERE session_id = ?');
    $stmt->execute([$session_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;
    return ($row['questions_answered'] < $row['total_questions']);
}
