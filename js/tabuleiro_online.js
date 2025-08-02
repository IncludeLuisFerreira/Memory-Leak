const tabuleiro = document.querySelector('.tabuleiro');
const timerDisplay = document.getElementById('timerDisplay');
const endGameScreen = document.getElementById('endGameScreen');
const endGameTime = document.getElementById('endGameTime');
const endGameErrors = document.getElementById('endGameErrors');

const userId = Number(document.getElementById('userId').value);
const salaId = Number(document.getElementById('salaId').value);

const idToImageMap = {
    1: 'freackle.png',
    2: 'mitzi.jpg',
    3: 'mordecai.png',
    4: 'nicomed.jpeg',
    5: 'pepper.png',
    6: 'rocky.jpg',
    7: 'serafine.jpg',
    8: 'viktor.png'
};

let estadoTabuleiro = null;
let turnoAtual = null;
let statusPartida = null;
let erros = 0;
let tempoInicio = null;
let timerInterval = null;
let jogoFinalizado = false;
let countdownShown = false;
let pollingIntervalId = null;
let travar = false;
let cartasViradasLocal = [];

// ================== Cronômetro ==================
function iniciarCronometro() {
    if (tempoInicio) return;
    tempoInicio = Date.now();
    timerInterval = setInterval(() => {
        const segundos = Math.floor((Date.now() - tempoInicio) / 1000);
        timerDisplay.textContent = 'Tempo: ' + segundos + 's';
    }, 1000);
}

function pararCronometro() {
    clearInterval(timerInterval);
    return Math.floor((Date.now() - tempoInicio) / 1000);
}

// ================== Renderização ==================
function criarCarta(cartaData, index) {
    const carta = document.createElement('div');
    carta.classList.add('carta');
    carta.dataset.index = index;

    const imageName = idToImageMap[cartaData.id] || 'verso.jpg';
    // Flip se virada, par ou temporariamente_virada
    const isFlipped = cartaData.virada || cartaData.par || cartaData.temporariamente_virada;
    if (isFlipped) carta.classList.add('flip');
    if (cartaData.par) carta.classList.add('matched');
    carta.innerHTML = `
        <div class="carta-inner">
            <div class="frente"><img src="/img/cartas/${imageName}" alt="Carta"></div>
            <div class="verso"><img src="/img/cartas/verso.jpg" alt="Verso"></div>
        </div>
    `;
    return carta;
}

// ================== Placar ==================
function atualizarPlacar() {
    const placarJogador1 = document.getElementById('placarJogador1');
    const placarJogador2 = document.getElementById('placarJogador2');
    if (!placarJogador1 || !placarJogador2 || !estadoTabuleiro) return;

    const paresJogador1 = estadoTabuleiro.pares_jogador1 || 0;
    const paresJogador2 = estadoTabuleiro.pares_jogador2 || 0;

    placarJogador1.textContent = `Jogador 1: ${paresJogador1} pares`;
    placarJogador2.textContent = `Jogador 2: ${paresJogador2} pares`;
}

function renderizarTabuleiro() {
    if (!estadoTabuleiro || !estadoTabuleiro.cartas) return;

    if (tabuleiro.children.length === 0) {
        estadoTabuleiro.cartas.forEach((carta, i) => tabuleiro.appendChild(criarCarta(carta, i)));
    } else {
        estadoTabuleiro.cartas.forEach((carta, i) => {
            const cartaElem = tabuleiro.children[i];
            // Flip na carta, não na carta-inner
            const isFlipped = carta.virada || carta.par || carta.temporariamente_virada;
            if (isFlipped && !cartaElem.classList.contains('flip')) {
                cartaElem.classList.add('flip');
            } else if (!isFlipped && cartaElem.classList.contains('flip')) {
                cartaElem.classList.remove('flip');
            }
            if (carta.par && !cartaElem.classList.contains('matched')) {
                cartaElem.classList.add('matched');
            } else if (!carta.par && cartaElem.classList.contains('matched')) {
                cartaElem.classList.remove('matched');
            }
        });
    }
}

function mostrarTurno() {
    const turnoDiv = document.querySelector('.turno');
    if (!turnoDiv) return;
    turnoDiv.textContent = (turnoAtual === userId) ? 'Seu turno' : 'Turno do adversário';
    turnoDiv.style.color = (turnoAtual === userId) ? 'green' : 'red';
}

// ================== Polling ==================
function atualizarEstado() {
    fetch(`../php/status_sala.php?sala_id=${salaId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'ok') return;

            estadoTabuleiro = data.tabuleiro;
            turnoAtual = Number(data.turno);
            statusPartida = data.status_partida;

            // Se o backend mandou desvirar cartas erradas, desvira imediatamente para todos
            if (estadoTabuleiro.desvirar_indices) {
                estadoTabuleiro.desvirar_indices.forEach(i => {
                    if (estadoTabuleiro.cartas[i]) {
                        delete estadoTabuleiro.cartas[i].temporariamente_virada;
                    }
                });
                delete estadoTabuleiro.desvirar_indices;
            }

            mostrarTurno();
            renderizarTabuleiro();
            atualizarPlacar();

            if (statusPartida === 'jogando' && !countdownShown) {
                countdownShown = true;
                mostrarContagemEAnimacao();
            }

            if (statusPartida === 'finalizada' && !jogoFinalizado) {
                jogoFinalizado = true;
                const tempoFinal = pararCronometro();
                endGameTime.textContent = 'Tempo: ' + tempoFinal + ' segundos';
                endGameErrors.textContent = 'Erros: ' + erros;
                endGameScreen.style.display = 'block';
                tabuleiro.style.display = 'none';
                pararPolling();
            }
        })
        .catch(err => console.error('Erro polling:', err));
}

function iniciarPolling() {
    if (!pollingIntervalId) pollingIntervalId = setInterval(() => {
        // Só faz polling se o tabuleiro existe
        if (!jogoFinalizado && !travar && estadoTabuleiro) {
            atualizarEstado();
        }
    }, 1000);
}

function pararPolling() {
    clearInterval(pollingIntervalId);
    pollingIntervalId = null;
}

// ================== Jogadas ==================
tabuleiro.addEventListener('click', e => {
    if (jogoFinalizado || travar) return;
    if (statusPartida !== 'jogando') return;

    const carta = e.target.closest('.carta');
    if (!carta) return;
    const index = Number(carta.dataset.index);

    if (userId !== turnoAtual) return;
    const cartaData = estadoTabuleiro.cartas[index];
    if (cartaData.par || cartaData.temporariamente_virada) return;

    // Só permite escolher cartas que não estão viradas/permanentes
    if (cartasViradasLocal.length >= 2) return;
    if (cartasViradasLocal.includes(index)) return;

    cartasViradasLocal.push(index);
    // Marca a carta como temporariamente virada no estado local
    estadoTabuleiro.cartas[index].temporariamente_virada = true;
    renderizarTabuleiro();

    // Só envia para o backend quando duas cartas forem escolhidas
    if (cartasViradasLocal.length === 2) {
        travar = true;
        fetch('../php/jogar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ indices: cartasViradasLocal, sala_id: salaId })
        })
            .then(res => res.json())
            .then(data => {
                turnoAtual = Number(data.turno);
                statusPartida = data.status_partida;
                estadoTabuleiro = data.tabuleiro;
                renderizarTabuleiro();
                mostrarTurno();
                atualizarPlacar();

                // Se o backend mandou desvirar cartas erradas
                if (data.tabuleiro.desvirar_indices) {
                    setTimeout(() => {
                        data.tabuleiro.desvirar_indices.forEach(i => {
                            if (estadoTabuleiro.cartas[i]) {
                                delete estadoTabuleiro.cartas[i].temporariamente_virada;
                            }
                        });
                        // Remove campo para não desvirar novamente
                        delete estadoTabuleiro.desvirar_indices;
                        travar = false;
                        cartasViradasLocal = [];
                        renderizarTabuleiro();
                    }, 500);
                } else {
                    // Par encontrado, cartas ficam viradas permanentemente
                    travar = false;
                    cartasViradasLocal = [];
                }

                // Fim de jogo: mostra tela e salva resultado
                if (statusPartida === 'finalizada') {
                    jogoFinalizado = true;
                    const tempoFinal = pararCronometro();
                    endGameTime.textContent = 'Tempo: ' + tempoFinal + ' segundos';
                    endGameErrors.textContent = 'Erros: ' + erros;
                    endGameScreen.style.display = 'block';
                    tabuleiro.style.display = 'none';
                    pararPolling();
                    // Salva resultado multiplayer
                    fetch('../php/salvar_partida.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `tempo=${tempoFinal}&modo=2&vencedor=${userId}`
                    });
                }
            })
            .catch(err => {
                console.error('Erro ao jogar:', err);
                travar = false;
                cartasViradasLocal = [];
            });
    }
});

// ================== Animação inicial ==================
function mostrarContagemEAnimacao() {
    const div = document.createElement('div');
    div.id = 'contagemMensagem';
    div.style.position = 'fixed';
    div.style.top = '50%';
    div.style.left = '50%';
    div.style.transform = 'translate(-50%, -50%)';
    div.style.fontSize = '48px';
    div.style.fontWeight = 'bold';
    div.style.backgroundColor = 'rgba(0,0,0,0.7)';
    div.style.color = '#fff';
    div.style.padding = '20px 40px';
    div.style.borderRadius = '10px';
    div.style.zIndex = '1000';
    document.body.appendChild(div);

    let count = 3;
    div.textContent = 'Começando em ' + count;

    const interval = setInterval(() => {
        count--;
        if (count > 0) {
            div.textContent = 'Começando em ' + count;
        } else {
            clearInterval(interval);
            div.remove();
            animarVirarCartas();
        }
    }, 1000);
}

function animarVirarCartas() {
    const todasCartas = document.querySelectorAll('.carta');
    todasCartas.forEach((carta, i) => {
        setTimeout(() => carta.classList.add('flip'), i * 150);
    });
    setTimeout(() => {
        todasCartas.forEach(carta => carta.classList.remove('flip'));
        iniciarCronometro();
        iniciarPolling();
    }, todasCartas.length * 150 + 1000);
}

// ================== Inicialização ==================
atualizarEstado();
iniciarPolling();
