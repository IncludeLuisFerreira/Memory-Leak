<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não logado']);
    header("Location: ../index.html");
    exit;
}

$cartas = range(1, 8); 
$tabuleiro = array_merge($cartas, $cartas);
shuffle($tabuleiro);

// Estrutura JSON
$estado = ['cartas' => []];
foreach ($tabuleiro as $i => $valor) {
    $estado['cartas'][] = ['id' => $valor, 'virada' => false, 'par' => false];
}

$estado_json = json_encode($estado);

$jogador1 = $_SESSION['id_usuario'];

$sql = "INSERT INTO Salas (jogador1_id, estado_tabuleiro, turno) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $jogador1, $estado_json, $jogador1);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'sala_id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao criar sala']);
}
?>
