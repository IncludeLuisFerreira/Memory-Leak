<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_usuario = trim($_POST['email_usuario']);
    $senha_usuario = $_POST['senha_usuario'];

    // Buscar o usuário pelo e-mail
    $sql = "SELECT id, nome, senha_hash FROM Usuarios WHERE email=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email_usuario);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            // Verifica a senha com password_verify
            if (password_verify($senha_usuario, $row['senha_hash'])) {
                // Senha correta -> Inicia a sessão
                $_SESSION['logado'] = true;
                $_SESSION['id_usuario'] = $row['id'];
                $_SESSION['nome_usuario'] = $row['nome'];

                header("Location: menu.php");
                exit;
            } else {
               header("Location: ../login.html?erro=senha");
                exit;
            }
        } else {
            header("Location: ../login.html?erro=email");
        }
    } else {
        echo "Erro na preparação da consulta.";
    }
}
?>
