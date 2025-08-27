// COnfigurações do jogo
const gameConfig = {
    cardTypes: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
    totalCards: 16,
    flipDelay: 1000
};

//Estado do jogo
let gameState = {
    cards: [],
    flippedCards: [],
    matchedCards: [],
    players: {},
    currentPlayer: null,
    gameStarted: false,
    socket: null
};

//Inicialização
document.addEventListener('DOMContentLoaded', () => {
    initializeGame();
    setupSocketConnection();
});

function initializeGame() {
    createCards();
    document.getElementById('startGame'). addEventListener('click', startGame);
    document.getElementById('leaveGame').addEventListener('click', leaveGame);
}

function createCards(){
    const gameBoard = document.getElementById('gameBoard');
    gameBoard.innerHTML = '';


    //Cria pares de cartas
       const cardPares = [];
        gameConfig.cardTypes.forEach(type => {
            cardPares.push(type, type);
    });

    //Embaralha as cartas
    cardPares.sort(() => Math.random() - 0.5);

    //Cria elementos HTML
    gameState.cards = cardPares.map((type, index) => {
        const card = document.createElement('div');
        card.className = 'card';
        card.dataset.index = index;
        card.dataset.type = type;
        card.addEventListener('click', () => flipCard(card));
        gameBoard.appendChild(card);
        return{
            element: card,
            type: type,
            flipped: false,
            matched: false
        };
    });
}

function setupSocketConnection(){
    //Usuando WebSocket para a comunicação em tempo real
    gameState.socket = new WebSocket('ws://localhost:8080?username=${config.username}&room=${config.room}');

    gameState.socket.onopen = () => {
        console.log('Conexão estabelecida');
    };

    gameState.socket.onmessage = (event) => {
        const message = JSON.parse(event.data);
        handleSocketMessage(message);
    };

    gameState.socket.onclose = () => {
        console.log('Conexão fechada');
    };
}

function handleSocketMessage(message){
    switch(message.type){
        case 'player_joined':
            updateOpponent(message.player);
            break;
        case 'player_left':
            opponentLeft();
            break;
        case 'game_started':
            startGame();
            break;
        case 'card_flipped':
            updateCard(message.cardIndex, message.playerId);
            break;
        case 'card_matched':
            updateMathedCards(message.cardIndices, message.playerId);
            break;
        case 'game_over':
            endGame(message.winner);
            break;
    }
}

function flipCard(card){
    if(!gameState.gameStarted || gameState.currentPlayer !== gameConfig.username) return;

    const index = parseInt(card.dataset.index);
    const cardState = gameState.cards[index];

    if(cardState.flipped || cardState.matched) return;

    //Vira carta localmente
    cardState.flipped = true;
    card.classList.add('flipped');
    card.textContent = cardState.type;

    //Envia para o servidor
    gameState.socket.send(JSON.stringify({
        type: 'flip_card',
        cardIndex: index,
        playerId: gameConfig.username
    }));

    //Verifica pares
    gameState.flippedCards.push(index);

    if(gameState.flippedCards.length === 2){
        checkForMatch();
    }
}

function checkForMatch(){
    const [firstIndex, secondIndex] = gameState.flippedCards;
    const firstCard = gameState.cards[firstIndex];
    const secondCard = gameState.cards[secondIndex];

    if(firstCard.type === secondCard.type){
        //par encontrado
        firstCard.matched = true;
        secondCard.matched = true;

        gameState.socket.send(JSON.stringify({
            type: 'match_cards',
            cardIndices: [firstIndex, secondIndex],
            playerId: gameConfig.username
        }));

        updateScore();
    }else{
        //Virar cartas de volta após de um delay
        setTimeout(() => {
            firstCard.flipped = false;
            secondCard.flipped = false;

            gameState.cards[firstIndex].element.classList.remove('flipped');
            gameState.cards[secondIndex].element.classList.remove('flipped');

            gameState.cards[firstIndex].element.textContent = '';
            gameState.cards[secondIndex];element.textContent = '';

            //Passa a vez
            switchPlayer();
        }, gameConfig.flipDelay);
    }
    gameState.flippedCards = [];
}

function updateScore(){
    //Atualiza placar local
    const yourScore = gameState.cards.filter(c => c.matched). length / 2;
    document.querySelector('.player.you.score').textContent = yourScore;

    //Verificar fim de jogo
    if(yourScore === gameConfig.cardTypes.length){
        gameState.socket.send(JSON.stringify({
            type: 'game_over',
            winner: config.username
        }));
    }
}

function startGame(){
    if(gameState.gameStarted) return;

    gameState.gameStarted = true;
    gameState.currentPlayer = config.username;
    document.getElementById('startGame').disable = true;

    gameState.socket.send(JSON.stringify({
        type: 'start_game'
    }));
}

function endGame(winner){
    alert(winner === config.username ? 'Você ganhou!' : 'Você perdeu!');
    resetGame();
}

function resetGame(){
    gameState = {
        cards: [],
        flippedCards: [],
        matchedCards: [],
        players: {},
        currentPlayer: null,
        gameStarted: false,
        socket: gameState.socket
    };

    document.getElementById('startGame').disable = false;
    initializeGame();
}

function leaveGame(){
    gameState.socket.close();
    window.location.href = 'index.php';
}