<?php
session_start();
require_once 'config.php';

// Verifica se usuário está logado
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nome_usuario'])) {
    header("Location: ../login.html");
    exit;
}

$nome = $_SESSION['nome_usuario'];
$id = $_SESSION['id_usuario'];

if (isset($_GET['action']) && $_GET['action'] === 'json') {
    header('Content-Type: application/json');

    $sql = "SELECT * FROM Partidas WHERE usuario_id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $partidas = [];
            while ($row = $resultado->fetch_assoc()) {
                $partidas[] = $row;
            }
            echo json_encode($partidas);
        } else {
            echo json_encode([]);
        }
    } else {
        echo json_encode(['error' => 'Erro na preparação da consulta.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Histórico de Partidas - Memory Leak</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/historico.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Histórico de Partidas</h1>

        <div id="historicoContainer"></div>

        <div class="buttons" style="margin-top: 30px;">
            <a href="menu.php" class="btn">Voltar ao Menu</a>
        </div>
    </div>
</body>
<script src="../js/historico.js"></script>
</html>
