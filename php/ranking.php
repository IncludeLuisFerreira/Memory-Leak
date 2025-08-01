<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nome_usuario'])) {
    header("Location: ../login.html");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'json') {
    header('Content-Type: application/json');

   $sql = "SELECT u.nome, r.total_partidas, r.vitorias, r.tempo_medio
        FROM Ranking r
        JOIN Usuarios u ON u.id = r.usuario_id
        ORDER BY r.tempo_medio Asc
        LIMIT 10;
    ";
   $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();
        
        $resultado = $stmt->get_result();

        while ($row = $resultado->fetch_assoc()) {
            $produto[] = $row;
        }

        echo json_encode($produto);
    }
}
?>