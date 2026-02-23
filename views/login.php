<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Memory Leak</title>
    <link rel="stylesheet" href="/assets/css/login.css?v='1'">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lora&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=Prohibition&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://img.icons8.com/?size=100&id=y3eyJSgDBAUa&format=png&color=000000" type="image/x-icon">
</head>
<body>
    <div class="container">
            <h1 class="title">Login</h1>
            <p class="description">
                Entre na sua conta para jogar <strong>Memory Leak</strong>.
            </p>

            <div id="error">
                
            </div>

            <div class="back-btn">
                <a href="/" class="btn-back">← Voltar</a>
            </div>

            <form action="/login" method="POST" class="form">
                <div class="form-group">
                    <label for="email_usuario">E-mail</label>
                    <input type="email" name="email_usuario" id="email_usuario" required>
                </div>

                <div class="form-group">
                    <label for="senha_usuario">Senha</label>
                    <input type="password" name="senha_usuario" id="senha_usuario" required>
                </div>

                <div class="form-buttons">
                    <input type="submit" class=" btn btn-primary" value="Entrar">
                    <input type="reset" value="Limpar" class="btn btn-secondary">
                </div>
            </form>

            <div class="login-footer">
                Não tem uma conta? <a href="/cadastro">Cadastre-se</a>
            </div>


            <footer>
                <p>&copy; 2025 Memory Leak — Todos os direitos reservados.</p>
            </footer>
        </div>
    </div>
</body>
<script src="/assets/js/erro.js"></script>
</html>
