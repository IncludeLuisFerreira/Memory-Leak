<?php

$host = "localhost";
$usuario = "root";
$senha = "Luis18.EC";
$banco = "MemoryLeak";

$conn = new mysql($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " .$conn->connect_error);
}

?>