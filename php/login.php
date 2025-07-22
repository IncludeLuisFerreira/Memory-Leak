<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === POST) {
    $email_usuario = $_POST['email_usuario'];
    $senha_usuario = $_POST['senha_usuario'];
    
    $sql = "SELECT * FROM Usuarios WHERE email=? AND senha=?";
    
}