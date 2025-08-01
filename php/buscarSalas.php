<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../index.html");
    exit;
}

$sql = "SELECT s.id AS sala_id, u.nome AS criador
        FROM Salas s
        JOIN Usuarios u ON s.jogador1_id = u.id
        WHERE s.status = 'esperando'
        LIMIT 20";
$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    $resultado = $stmt->get_result();

    while ($row = $resultado->fetch_assoc()) {
        $salas[] = $row;
    }

    echo json_encode($salas);
}

