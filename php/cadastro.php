<?php

require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = $_POST['nome_usuario'];
    $email_usuario = $_POST['email_usuario'];
    $senha_usuario = $_POST['senha_usuario'];
    $senha_usuario_confirmar = $_POST['senha_usuario_confirmar'];

    // Confirmar se o usuário colocou a mesma senha
    if ($senha_usuario !== $senha_usuario_confirmar) {
        // Voltar mensagem de erro para cadastro.html
        $erro = urlencode("As senhas não coicidem");
        header("Location: ../cadastro.html?erro=$erro");
        exit;
    }

    $sql_verifica = "SELECT id FROM Usuarios WHERE email=?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    
    if ($stmt_verifica) {
        $stmt_verifica->bind_param("s", $email_usuario);
        $stmt_verifica->execute();
        $stmt_verifica->store_result();
        
        if ($stmt_verifica->num_rows > 0) {
            $erro = urlencode("E-mail já cadastrado.");
            header("Location: ../cadastro.html?erro=$erro");
        }
        $stmt_verifica->close();
    }
    else {
        $erro = urlencode("Erro ao preparar verificação de e-mail.");
        header("Location: ../cadastro,html?erro=$erro");
        exit;
    }
    
    $senha_hash = password_hash($senha_usuario, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuarios (nome, email, senha) VALUES(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    

    if ($stmt) {
        $stmt->bind_param("sss", $nome_usuario, $email_usuario, $senha_hash);
        
        // Sucesso: redirecionar para login 
        if ($stmt->execute()) {
            $_SESSION['id_usuario'] = $conn->insert_id;
            $_SESSION['nome_usuario'] = $nome_usuario;
            header("Location: ../menu.php");
            exit;
        }
        else {
            $erro = urlencode("Erro ao cadastrar. Tente novamente.");
            header("Location: ../cadastro.html?erro=$erro");
            exit;        }
    }
    else {
        $erro = urlencode("Erro ao preparar o cadastro.");
        header("Location: ../cadastro.html?erro=$erro");
        exit;
    }
    
}