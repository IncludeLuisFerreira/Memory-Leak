<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Memory Leak</title>
    <!-- Fonte Art Déco e fonte para textos -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/menu.css">
    <link rel="stylesheet" href="/assets/css/historico.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Bem-vindo, <?= htmlspecialchars($nome); ?></h1>

        <div class="back-btn">
            <a href="/logout" class="btn-back">← Sair</a>
        </div>

        <div class="buttons">
            <a href="/solo" class="btn">Jogar Sozinho</a>
            <a href="/online" class="btn">Jogar Online</a>
            <a href="/historico" class="btn">Histórico de Partidas</a>
        </div>

        <div class="ranking">
            <h2 class="ranking-title">Placar de Ranking</h2>
            <div id="rankingContainer"></div>
        </div>

    </div>
</body>
<script src="/assets/js/ranking.js"></script>
</html>
