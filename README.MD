
# Jogo da memória 
 Um tabuleiro do jogo da memória 4x4, com o tema inspirado na animação do Youtube *Lackadaisy*, que se passa em 1927, durante a Lei Seca nos Estados Unidos. O jogo oferece dois modos de jogo: 1 jogador e 2 jogadores.

![tela principal](https://i.imgur.com/6xU7OW2.png)
    
## Funcionalidades
*Modo de Jogo*

Modo 1 jogador:
* Jogador clica nas cartas para revelá-las;
* Sistem verifica se as cartas selecionadas formas um par;
* Pares corretos permanecem virados;
* Encontre todos os pares no menor tempo;
* Timer registra o tempo total da partida.

Modo 2 jogadores (Online):
* Jogadores alternam turnos;
* Acerto = jogador continua;
* Erro = passa a vez para o oponente;
* Vitória para quem tiver o maior número de pares encontrados;
* Timer por turno: tempo limite para selecionar as cartas;
* Resultados salvos com pontuação e vencedor.

![img](https://i.imgur.com/g99zcri.png)

## Tecnologias Utilizadas
*Frontend*  
    Interface com HTML e CSS;  
    Uso de JavaScript para a lógica do jogo;  
    Comunicação com o backend via AJAX(fetch).

*Backend com PHP*  
    Controle de sessão do jogo(estado das cartas, vez do jogador);  
    Sripts PHP para:  
 * Verificar pares;  
 * Registrar partidas;  
 * Buscar ranking;  
 * Fazer login/cadastro.   

*Banco de Dados(MySQL)*  
    Tabelas:  
    1. usuarios(id, nome, email, senha_hash);  
    2. partidas(id, usuario_id, data, tempo,modo,vencedor, pontos);  
    3. ranking(usuario_id, total_partidas, vitorias, tempo_medio).  
    A senha é criptografada antes de ser salva no banco de dados.
## Sistema de Autenticação
* Cadastro e login de usuários;
* Sessões ativas durante o jogo;
* Acesso restrito a usuários logados;
* Senhas criptografadas antes do armazenamento.

## Histórico e Ranking
* Registro completo de todas as partidas;
* Histórico pessoal de jogadas;
* Ranking geral baseado em:
    * Número de vitórias (online);
    * Tempo médio(1 jogador).
## Requisitos para Execução
* Servidor web com PHP;
* Banco de dados MySQL;
* Navegador moderno com JavaScript habilitado.
## Vídeo Explicativo
[MemoryLeak-vídeo](https://youtu.be/GRJBlHqf5uM)

## Link do site hospedado
* [index.html](https://memoryleak.rf.gd/index.html)

## Autores

* Professor:
    * [Ricardo Ramos de Oliveira](ricardo.ramos@ifsuldeminas.edu.br) 
* Alunos:
    * [Luís Felipe Costa Ferreira](https://github.com/IncludeLuisFerreira)
    * [Milena Evelyn dos Reis Silva(Nick)](https://github.com/Lynnes42)






