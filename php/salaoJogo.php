<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../css/salaoJogo.css" />
    <title>Memory Leak</title>
</head>
<body>
    <a href="menu.php" id="backToMenu">Voltar ao Menu</a>
    <div class="game-container">
        <button id="startGameBtn">Começar Jogo</button>
        <div id="timerDisplay">Tempo: 0 segundos</div>
        <div class="tabuleiro"></div>
    </div>
    <div id="endGameScreen">
        <h2>Parabéns!</h2>
        <p id="endGameTime">Tempo: 0 segundos</p>
        <p id="endGameErrors">Erros: 0</p>
        <button id="restartGameBtn">Jogar Novamente</button>
        <button id="backToMenuBtn">Voltar ao Menu</button>
    </div>

</body>
<script src="../js/tabuleiro.js"></script>
<script src="../js/tocarSom.js"></script>
</html>
