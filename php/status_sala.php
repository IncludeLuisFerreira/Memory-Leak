<?php
require_once 'config.php';
session_start();

/**
 * Endpoint para retornar o estado atual da sala de jogo.
 * Parâmetros esperados:
 * - sala_id (GET): ID da sala a ser consultada.
 * Retorna JSON com o estado do tabuleiro, turno atual e status da partida.
 */

if (!isset($_GET['sala_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID da sala não informado.']);
    exit;
}

$sala_id = intval($_GET['sala_id']);

$sql = "SELECT estado_tabuleiro, turno, status FROM Salas WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na preparação da consulta.']);
    exit;
}
$stmt->bind_param("i", $sala_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala não encontrada.']);
    exit;
}

$row = $res->fetch_assoc();
$estado = json_decode($row['estado_tabuleiro'], true);

if ($estado === null) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao decodificar o estado do tabuleiro.']);
    exit;
}

echo json_encode([
    'status' => 'ok',
    'tabuleiro' => $estado,
    'turno' => intval($row['turno']),
    'status_partida' => $row['status']
]);
?>
