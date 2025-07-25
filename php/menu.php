<?php
session_start();

// Verifica se usuário está logado
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nome_usuario'])) {
    header("Location: ../login.html"); // Corrigido aqui
    exit;
}

$nome = $_SESSION['nome_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Memory Leak</title>
    <!-- Fonte Art Déco e fonte para textos -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Bem-vindo, <?php echo htmlspecialchars($nome); ?></h1>

        <div class="buttons">
            <a href="salaoJogo.php" class="btn">Jogar Sozinho</a>
            <a href="online.php" class="btn">Jogar Online</a>
        </div>

        <div class="ranking">
            <h2 class="ranking-title">Placar de Ranking</h2>
            <ul class="ranking-list">
            </ul>
        </div>
    </div>
</body>
</html>
