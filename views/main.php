<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Memory Leak - Main</title>
    <link rel="stylesheet" href="/assets/css/menu.css" />
    <link rel="stylesheet" href="/assets/css/main.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=Prohibition&display=swap" rel="stylesheet" />
</head>
<body>
    <header>
        <h1>Memory Leak</h1>
        <nav>
            <a href="/" target="contentFrame">Início</a>
            <a href="/login" target="contentFrame">Login</a>
            <a href="/cadastro" target="contentFrame">Cadastro</a>
        </nav>
        <audio id="backgroundMusic" loop volume="0.3" preload="auto">
            <source src="/assets/audio/cheerful-promenade-easy-going-electro-swing-composition-for-blogs-149595.mp3" type="audio/mpeg" />
            Seu navegador não suporta o elemento de áudio.
        </audio>
    </header>
    <iframe id="contentFrame" name="contentFrame" src="/" title="Conteúdo"></iframe>

    <script>
        // Play background music on page load
        const audio = document.getElementById('backgroundMusic');
        audio.volume = 0.3;
        audio.play().catch(e => console.error('Erro ao reproduzir música:', e));
    </script>
</body>
</html>
