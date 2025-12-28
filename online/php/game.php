<?php
session_start();
if(!isset($_POST['username'])){
    header("Location: index.php");
    exit();
}

$_SESSION['username'] = htmlspecialchars($_POST['username']);
$room = $_POST['room'] === 'create' ? uniqid() : 'default';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de jogo - <?php echo $_SESSION['username']; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h2>Sala: <?php echo $room; ?></h2>
            <div class="players">
                <div class="player you">
                    <span class="score">0</span>
                </div>
                <div class="player opponent">
                    <span>Aguardando oponente...</span>
                    <span class="score">0</span>
                </div>
            </div>
        </div>

        <div class="game-board" id="gameboard"></div>
        <div class="game-controls">
            <button id="startGame">Iniciar jogo</button>
            <button id="leaveGame">Sair  da sala</button>
        </div>
    </div>

    <script>
        const config = {
            username: "<?php echo $_SESSION['username']; ?>",
            room: "<?php echo $room; ?>"
        };
    </script>
    <script src="script.js"></script>
</body>
</html>