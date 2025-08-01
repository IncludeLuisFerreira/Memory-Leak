<?php
header('Content-Type: application/json');

/**
 * Endpoint para comparar duas cartas.
 * Recebe via POST os parâmetros:
 * - img1: URL da primeira carta
 * - img2: URL da segunda carta
 * Retorna JSON com 'match' booleano indicando se as cartas são iguais.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $img1 = isset($_POST['img1']) ? filter_var($_POST['img1'], FILTER_SANITIZE_STRING) : '';
    $img2 = isset($_POST['img2']) ? filter_var($_POST['img2'], FILTER_SANITIZE_STRING) : '';

    // Compara apenas o nome do arquivo da imagem para evitar problemas com caminhos
    $nomeImg1 = basename($img1);
    $nomeImg2 = basename($img2);

    $match = ($nomeImg1 === $nomeImg2);

    echo json_encode(['match' => $match]);
} else {
    echo json_encode(['error' => 'Método de requisição inválido']);
}
