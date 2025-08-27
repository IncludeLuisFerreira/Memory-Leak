<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../css/online.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=Prohibition&display=swap" rel="stylesheet">
    <title>Memory Leak</title>
</head>
<body>
    <a href="menu.php" id="backToMenu">Voltar ao Menu</a>
    <div class="container">
        <div class="presentation">
            <h1 class="title">Online</h1>
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
            <p>Entre ou crie uma sala para competir, cada partida agrega no seu ranking</p>
        </div>
        <div class="buttons">
            <button id="criarSalaBtn" class="btn">Crie uma sala</button>
            <button id="entrarSalaBtn" class="btn">Entrar em uma sala</button>
        </div>
        <div id="statusMsg" class="status"></div>
        <div class="tabuleiro"></div>
        <div id="salasContainer"></div>
    </div>
</body>
<script src="../js/online.js"></script>
</html>
