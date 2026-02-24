<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Memory Leak</title>
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=Prohibition&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/cadastro.css">
</head>
<body>
    <div class="cadastro-container">
        <h1 class="title">CADASTRO</h1>

        <div id="error">

        </div>

        <div class="back-btn">
            <a href="/" class="btn-back">← Voltar</a>
        </div>

        
        <form class="cadastro-form" action="/cadastro" method="POST">
            <div class="form-group">
                <label for="nome">NickName</label>
                <input type="text" id="nome" name="nome_usuario" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email_usuario" required>
            </div>
                        
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha_usuario" required>
            </div>
            
            <div class="form-group">
                <label for="confirmar-senha">Confirmar Senha</label>
                <input type="password" id="confirmar-senha" name="senha_usuario_confirmar" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <button type="reset" class="btn btn-secondary">Limpar</button>
            </div>
        </form>
        
        <div class="cadastro-footer">
            Já tem uma conta? <a href="/login">Faça login</a>
        </div>

        <footer>
            <p>&copy; 2025 Memory Leak — Todos os direitos reservados.</p>
        </footer>
    </div>
</body>
<script src="/assets/js/erro.js"></script>
</html>
