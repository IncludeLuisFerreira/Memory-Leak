<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$salaId = $data['sala_id'];
$index = $data['index'];

$caminhoSala = "../salas/{$salaId}.json";

if (!file_exists($caminhoSala)) {
    echo json_encode(["success" => false, "message" => "Sala não encontrada"]);
    exit;
}

$estado = json_decode(file_get_contents($caminhoSala), true);

// Marca a carta como temporariamente virada
if (isset($estado['cartas'][$index])) {
    $estado['cartas'][$index]['temporariamente_virada'] = true;
    file_put_contents($caminhoSala, json_encode($estado));
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Carta inválida"]);
}
?>
