<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/assets/css/salaoJogo.css" />
    <title>Jogo da Memória - Modo Solo</title>
</head>
<body>
    <a href="/menu" id="backToMenu">Voltar ao Menu</a>
    <div class="game-container">
        <button id="startGameBtn">Começar Jogo</button>
        <div id="timerDisplay">Tempo: 0 segundos</div>
        <div class="tabuleiro" aria-label="Tabuleiro do jogo da memória" role="grid"></div>
    </div>
    <div id="endGameScreen" role="dialog" aria-modal="true" aria-labelledby="endGameTitle" style="display:none;">
        <h2 id="endGameTitle">Parabéns!</h2>
        <p id="endGameTime">Tempo: 0 segundos</p>
        <p id="endGameErrors">Erros: 0</p>
        <button id="restartGameBtn" aria-label="Jogar novamente">Jogar Novamente</button>
        <button id="backToMenuBtn" aria-label="Voltar ao menu">Voltar ao Menu</button>
    </div>

</body>
<script src="/assets/js/tabuleiro.js"></script>
<script src="/assets/js/tocarSom.js"></script>
</html>
