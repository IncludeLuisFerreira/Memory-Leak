<?php
require_once 'config.php';
session_start();

if (!isset($_GET['sala_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID da sala não informado.']);
    exit;
}

$sala_id = intval($_GET['sala_id']);

$sql = "SELECT estado_tabuleiro, turno, status FROM Salas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sala_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala não encontrada.']);
    exit;
}

$row = $res->fetch_assoc();
$estado = json_decode($row['estado_tabuleiro'], true);

echo json_encode([
    'status' => 'ok',
    'tabuleiro' => $estado,
    'turno' => intval($row['turno']),
    'status_partida' => $row['status']
]);
?>
