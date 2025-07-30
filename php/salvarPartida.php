<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $tempo = isset($_POST['tempo']) ? intval($_POST['tempo']) : 0;
    $modo = isset($_POST['modo']) ? $_POST['modo'] : '1';
    $vencedor = isset($_POST['vencedor']) ? intval($_POST['vencedor']) : 1;
    $pontos = isset($_POST['pontos']) ? intval($_POST['pontos']) : 0;

    if ($id_usuario <= 0) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    
    $sql = "INSERT INTO Partidas (usuario_id, tempo, modo, vencedor, pontos) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiii", $id_usuario, $tempo, $modo, $vencedor, $pontos);

    // Salvar as métricas para ranking(precisa terminar)
    $sql_ranking = "INSERT INTO Ranking (usuario_id, total_partidas, vitorias, tempo_medio)
    VALUES (?, 1, IF(?=1,1,0), ?)
    ON DUPLICATE KEY UPDATE
        total_partidas = total_partidas + 1,
        vitorias = vitorias + IF(? = 1, 1, 0),
        tempo_medio = ((tempo_medio * (total_partidas - 1)) + ?) / total_partidas";
    
    $stmt_ranking = $conn->prepare($sql_ranking);
    $stmt_ranking->bind_param("iiiii", $id_usuario, $vencedor, $tempo, $vencedor, $tempo);
    
    if ($stmt->execute() && $stmt_ranking->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['error' => 'Failed to save game']);
    }

    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
