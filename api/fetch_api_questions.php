<?php
// api/fetch_api_questions.php - Fetches questions from Open Trivia DB API and returns a random one
header('Content-Type: application/json');

function fetchOpenTriviaQuestion($difficulty = null) {
    $url = 'https://opentdb.com/api.php?amount=50&category=18&type=multiple';
    $response = file_get_contents($url);
    if ($response === false) {
        return null;
    }
    $data = json_decode($response, true);
    if (!isset($data['results']) || empty($data['results'])) {
        return null;
    }
    // Optionally filter by difficulty
    $questions = $data['results'];
    if ($difficulty) {
        $questions = array_filter($questions, function($q) use ($difficulty) {
            return strtolower($q['difficulty']) === strtolower($difficulty);
        });
        $questions = array_values($questions);
    }
    if (empty($questions)) {
        return null;
    }
    // Pick a random question
    $question = $questions[array_rand($questions)];
    // Format to match local DB structure
    return [
        'id' => 'api_' . md5($question['question']),
        'question' => html_entity_decode($question['question']),
        'choices' => array_map('html_entity_decode', array_merge($question['incorrect_answers'], [$question['correct_answer']])),
        'correct_answer' => html_entity_decode($question['correct_answer']),
        'difficulty' => ucfirst($question['difficulty']),
        'source' => 'api'
    ];
}

$difficulty = $_GET['difficulty'] ?? null;
$question = fetchOpenTriviaQuestion($difficulty);
if (!$question) {
    echo json_encode(['success' => false, 'message' => 'No API questions available']);
    exit();
}
// Shuffle choices
shuffle($question['choices']);
echo json_encode(['success' => true, 'question' => $question]);
