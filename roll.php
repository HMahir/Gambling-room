<?php
session_start();

// Check if the game is active
if (!isset($_SESSION['players']) || !isset($_SESSION['current_player']) || !isset($_SESSION['current_round'])) {
    echo json_encode(['error' => 'Game not active']);
    exit;
}

$currentPlayerIndex = $_SESSION['current_player'];
$diceCount = $_SESSION['dice_count'] ?? 1;

// Roll the dice
$total = 0;
$rolled = [];
for ($i = 0; $i < $diceCount; $i++) {
    $roll = rand(1, 6);
    $rolled[] = $roll;
    $total += $roll;
}

// Update score
$_SESSION['players'][$currentPlayerIndex]['score'] += $total;

$response = [
    'index' => $currentPlayerIndex,
    'name' => $_SESSION['players'][$currentPlayerIndex]['name'],
    'dice' => $rolled,
    'sum' => $total,
    'score' => $_SESSION['players'][$currentPlayerIndex]['score'],
    'round' => $_SESSION['current_round'],
];

// Advance turn
$_SESSION['current_player']++;

if ($_SESSION['current_player'] >= count($_SESSION['players'])) {
    $_SESSION['current_player'] = 0;
    $_SESSION['current_round']++;
}

// Check if game is now over
if ($_SESSION['current_round'] > $_SESSION['rounds']) {
    $response['game_over'] = true;
} else {
    $response['next_player'] = $_SESSION['current_player'];
    $response['next_name'] = $_SESSION['players'][$_SESSION['current_player']]['name'];
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
