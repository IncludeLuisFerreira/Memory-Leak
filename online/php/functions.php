<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function saveGameResult($pdo, $winner, $loser, $score) {
    $stmt = $pdo->prepare("INSERT INTO game_results (winner, loser, score, played_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$winner, $loser, $score]);
}

function getLeaderboard($pdo) {
    $stmt = $pdo->query("
        SELECT username, COUNT(*) as wins 
        FROM (
            SELECT winner as username FROM game_results
            UNION ALL
            SELECT loser as username FROM game_results
        ) as players
        GROUP BY username
        ORDER BY wins DESC
        LIMIT 10
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}