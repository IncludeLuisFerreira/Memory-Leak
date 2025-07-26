<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $img1 = isset($_POST['img1']) ? $_POST['img1'] : '';
    $img2 = isset($_POST['img2']) ? $_POST['img2'] : '';

    // Compare the two image URLs
    $match = ($img1 === $img2);

    echo json_encode(['match' => $match]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
