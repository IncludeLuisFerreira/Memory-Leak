<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/assets/css/salaoJogo.css" />
    <title>Memory Leak - Online</title>
</head>
<body>
    <input type="hidden" id="userId" value="<?= $userId ?>">
    <input type="hidden" id="salaId" value="<?= $room->getId() ?>">

    <a href="/menu" id="backToMenu">Voltar ao Menu</a>

    <div class="game-container">
        <div class="status-bar">
            <div class="turno">Aguardando adversário...</div>
            <div id="placar">
                <span id="placarJogador1">Jogador 1: 0</span> |
                <span id="placarJogador2">Jogador 2: 0</span>
            </div>
        </div>

        <div id="timerDisplay">Tempo: 0 segundos</div>
        <div class="tabuleiro" aria-label="Tabuleiro" role="grid"></div>
    </div>

    <div id="endGameScreen" style="display:none;">
        <h2 id="endGameTitle">Fim de Jogo!</h2>
        <p id="endGameTime"></p>
        <p id="endGameErrors"></p>
        <button onclick="location.href='/menu'">Voltar ao Menu</button>
    </div>

    <script src="/assets/js/tabuleiro_online.js"></script>
    <script src="/assets/js/tocarSom.js"></script>
</body>
</html>
