<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Leak</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=Prohibition&display=swap" rel="stylesheet">
</head>
<body>
    <!--Tela de aviso (pensei em deixar para avisar como se joga no online)-->
    <div class="warning-screen" id="warningScreen">
        <div class="warning-plaque">
            <h2 class="warning-title">Desafio de Memória Online</h2>
            <p class="warning-text">Você está prestes a entrar em um emocionante desafio de memória!</p>
            <p class="warning-text">- Insira seu nome para se identificar no jogo.</p>
            <p class="warning-text">- Selecione uma sala para competir com outros jogadores.</p>
            <p class="warning-text">- O objetivo é encontrar todos os pares de cartas o mais rápido possível.</p>
            <p class="warning-text">- O jogador que acumular mais pontos será o campeão. Boa sorte!</p>
            <button class="continue-btn" id="continueBtn">Vamos Jogar!</button>
        </div>
    </div>

    <!--Conteudo principal-->
    <div class="main-content" id="mainContent">
        <div class="corner top-left"></div>
        <div class="corner top-right"></div>
        <div class="corner bottom-left"></div>
        <div class="corner bottom-right"></div>

        <div class="vinyl">
            <div class="vinyl-center"></div>
            <div class="vinyl-label-small">Memory<br>Leak</div>
        </div>

        <div class="speakeasy-poster fade-in">
            <h1 class="poster-title">Memory Leak</h1>
            <p class="poster-subtitle">
                Bem-vindo ao <strong>Memory Leak</strong>, um jogo da memória online que desafia sua mente 
                e reflexos! Cadastre-se, faça login e entre nesta experiência única.
            </p>
            <p class="vinyl-label">Memory Leak • 2025

            <div class="menu">
                <a href="/login" class="menu-item">Login</a>
                <a href="/cadastro" class="menu-item">Cadastro</a>
            </div>

            <footer>
                <p class="footer">&copy; 2025 Memory Leak — Todos os direitos reservados.</p>
            </footer>
        </div>
    </div>
    <script src="/assets/js/script.js"></script>
</body>
</html>
