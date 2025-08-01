<?php
session_start();
require_once 'config.php'; // Conexão com o banco

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logado'], $_SESSION['id_usuario']) || $_SESSION['logado'] !== true) {
    header('Location: ../index.html');
    exit;
}

if (isset($_GET['acao']) && $_GET['acao'] === 'sair') {
    $sala_id = intval($_GET['sala']);
    $id_usuario = $_SESSION['id_usuario'];

    // Verifica se a sala existe e se o usuário faz parte dela
    $sql = "SELECT * FROM Salas WHERE id = ? AND (jogador1_id = ? OR jogador2_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $sala_id, $id_usuario, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sala = $result->fetch_assoc();

        if ($sala['jogador1_id'] === $id_usuario) {
            // Se for o jogador 1, exclui a sala
            $delete = $conn->prepare("DELETE FROM Salas WHERE id = ?");
            $delete->bind_param("i", $sala_id);
            $delete->execute();
        } elseif ($sala['jogador2_id'] === $id_usuario) {
            // Se for o jogador 2, remove ele e volta a sala para "esperando"
            $update = $conn->prepare("UPDATE Salas SET jogador2_id = NULL, status = 'esperando' WHERE id = ?");
            $update->bind_param("i", $sala_id);
            $update->execute();
        }
    }

    // Redireciona para menu
    header('Location: menu.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-Br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo Online</title>
    <link rel="stylesheet" href="../css/salaoJogoOnline.css">
</head>
<body>
    <input type="hidden" id="userId" value="<?php echo $_SESSION['id_usuario']; ?>">
    <input type="hidden" id="salaId" value="<?php echo htmlspecialchars($_GET['sala']); ?>">

    <!-- Botão para sair da sala -->
    <a href="?acao=sair&sala=<?php echo htmlspecialchars($_GET['sala']);?>" id="backToMenu">Voltar ao Menu</a>
    
    <!-- Cronômetro -->
    <div id="timerDisplay">Tempo: 0s</div>

    <div class="turno">
        
    </div>

    <!-- Placar -->
    <div id="placar" style="margin-bottom: 10px; font-size: 18px;">
        <span id="placarJogador1">Jogador 1: 0 pares</span> |
        <span id="placarJogador2">Jogador 2: 0 pares</span>
    </div>
    
    <!-- Tabuleiro do jogo -->
    <div class="tabuleiro"></div>
    
    <!-- Tela final -->
    <div id="endGameScreen" style="display:none;">
        <h2>Jogo Finalizado!</h2>
        <p id="endGameTime"></p>
        <p id="endGameErrors"></p>
        <a href="menu.php"><button>Voltar ao Menu</button></a>
    </div>

    <!-- Scripts -->
    <script src="../js/online.js"></script>
    <script src="../js/tabuleiro_online.js"></script>

</body>
</html>
