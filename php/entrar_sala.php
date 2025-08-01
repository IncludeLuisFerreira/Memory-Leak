<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não logado.']);
    exit;
}

if (!isset($_POST['sala_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID da sala não informado.']);
    exit;
}

$sala_id = intval($_POST['sala_id']);
$userId = $_SESSION['id_usuario'];

// Busca a sala
$sql = "SELECT jogador1_id, jogador2_id, status FROM Salas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sala_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala não encontrada.']);
    exit;
}

$sala = $res->fetch_assoc();

if ($sala['status'] === 'jogando') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala já está em jogo.']);
    exit;
}

// Define jogador 2 se vaga
if (is_null($sala['jogador2_id']) && $userId != $sala['jogador1_id']) {
    $sql = "UPDATE Salas SET jogador2_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $sala_id);
    $stmt->execute();
}

// Se agora tem 2 jogadores → inicia o jogo
$sqlCheck = "SELECT jogador1_id, jogador2_id FROM Salas WHERE id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $sala_id);
$stmtCheck->execute();
$dados = $stmtCheck->get_result()->fetch_assoc();

if ($dados['jogador1_id'] && $dados['jogador2_id']) {
    // Atualiza status para 'jogando' e define o turno para o jogador 1, sem alterar o estado do tabuleiro
    $status = 'jogando';
    $turno = $dados['jogador1_id']; // Criador começa

    $sqlStart = "UPDATE Salas SET status = ?, turno = ? WHERE id = ?";
    $stmtStart = $conn->prepare($sqlStart);
    $stmtStart->bind_param("sii", $status, $turno, $sala_id);
    $stmtStart->execute();
}

echo json_encode(['status' => 'ok']);
?>
