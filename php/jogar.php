<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

/**
 * Endpoint para processar a jogada do jogador.
 * Recebe JSON com:
 * - sala_id: ID da sala de jogo
 * - indices: array com dois índices das cartas selecionadas
 * Retorna JSON com o novo estado do tabuleiro, turno e status da partida.
 */

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não está logado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['sala_id']) || !isset($input['indices']) || !is_array($input['indices']) || count($input['indices']) !== 2) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos ou inválidos.']);
    exit;
}

$sala_id = intval($input['sala_id']);
$indices = $input['indices'];
$userId = $_SESSION['id_usuario'];

// Buscar estado atual da sala
$sql = "SELECT estado_tabuleiro, turno, jogador1_id, jogador2_id FROM Salas WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na preparação da consulta.']);
    exit;
}
$stmt->bind_param("i", $sala_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala não encontrada.']);
    exit;
}

$row = $res->fetch_assoc();
$estado = json_decode($row['estado_tabuleiro'], true);

if (!$estado || !isset($estado['cartas'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Estado inválido.']);
    exit;
}

if ($userId != $row['turno']) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não é seu turno.']);
    exit;
}

$cartas = &$estado['cartas'];

// Limpa flags 'temporariamente_virada' de jogadas anteriores para evitar cards travados virados
foreach ($cartas as &$carta) {
    if (isset($carta['temporariamente_virada'])) {
        unset($carta['temporariamente_virada']);
    }
}

// Validar cartas clicadas
foreach ($indices as $indice) {
    if (!isset($cartas[$indice]) || $cartas[$indice]['par']) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Carta inválida.']);
        exit;
    }
}

 // Virar as cartas clicadas (no backend, para manter controle)
foreach ($indices as $indice) {
    $cartas[$indice]['virada'] = true;
}

// Verifica se é par
$manter_turno = false;
if ($cartas[$indices[0]]['id'] === $cartas[$indices[1]]['id']) {
    // É par!
    $cartas[$indices[0]]['par'] = true;
    $cartas[$indices[1]]['par'] = true;
    $manter_turno = true;

    if ($userId == $row['jogador1_id']) {
        $estado['pares_jogador1']++;
    } else {
        $estado['pares_jogador2']++;
    }
} else {
    // Não é par, mantém as cartas viradas temporariamente para animação no frontend
    // Adiciona um campo 'temporariamente_virada' para controle frontend
    $cartas[$indices[0]]['temporariamente_virada'] = true;
    $cartas[$indices[1]]['temporariamente_virada'] = true;
}

// Define próximo turno
$proximo_turno = $manter_turno ? $userId : (($userId == $row['jogador1_id']) ? $row['jogador2_id'] : $row['jogador1_id']);

// Verifica se o jogo acabou
$jogoFinalizado = true;
foreach ($cartas as $c) {
    if (!$c['par']) {
        $jogoFinalizado = false;
        break;
    }
}
$novo_status = $jogoFinalizado ? 'finalizada' : 'jogando';

// Atualiza banco com novo estado
$novo_estado_json = json_encode($estado);
$sql_up = "UPDATE Salas SET estado_tabuleiro = ?, turno = ?, status = ? WHERE id = ?";
$stmt_up = $conn->prepare($sql_up);
if (!$stmt_up) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na preparação da atualização.']);
    exit;
}
$stmt_up->bind_param("sisi", $novo_estado_json, $proximo_turno, $novo_status, $sala_id);
$stmt_up->execute();

echo json_encode([
    'status' => 'ok',
    'tabuleiro' => $estado,
    'turno' => $proximo_turno,
    'status_partida' => $novo_status
]);
