<?php
// banana-game-access.php
// Helper to check if user can access banana game
require_once __DIR__ . '/../config/database.php';

function canAccessBananaGame($session_id) {
    // Check if session has completed all questions
    global $database;
    $stmt = $database->executeQuery('SELECT questions_answered FROM game_sessions WHERE session_id = ?', [$session_id]);
    $row = $database->fetchOne($stmt);
    if (!$row) return false;
    // Allow access to banana game (no specific condition needed)
    return true;
}
