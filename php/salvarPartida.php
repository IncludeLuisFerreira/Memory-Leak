<?php
header('Content-Type: application/json');
require_once 'config.php'; // Assuming this file contains DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : 0;
    $tempo = isset($_POST['tempo']) ? intval($_POST['tempo']) : 0;
    $modo = isset($_POST['modo']) ? $_POST['modo'] : '1';
    $vencedor = isset($_POST['vencedor']) ? intval($_POST['vencedor']) : 1;
    $pontos = isset($_POST['pontos']) ? intval($_POST['pontos']) : 0;

    if ($usuario_id <= 0) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Partidas (usuario_id, tempo, modo, vencedor, pontos) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiii", $usuario_id, $tempo, $modo, $vencedor, $pontos);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['error' => 'Failed to save game']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
