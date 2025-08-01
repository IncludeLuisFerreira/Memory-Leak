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
    carta.innerHTML = `
        <div class="carta-inner${(cartaData.virada || cartaData.par) ? ' flip' : ''}${cartaData.par ? ' matched' : ''}">
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
            const cartaInner = cartaElem.querySelector('.carta-inner');
            // Considera 'virada', 'par' e 'temporariamente_virada' para mostrar flip
            const isFlipped = carta.virada || carta.par || carta.temporariamente_virada;
            cartaInner.classList.toggle('flip', isFlipped);
            cartaInner.classList.toggle('matched', carta.par);
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
        if (!jogoFinalizado && !travar) atualizarEstado();
    }, 1000);
}

function pararPolling() {
    clearInterval(pollingIntervalId);
    pollingIntervalId = null;
}

// ================== Jogadas ==================
tabuleiro.addEventListener('click', e => {
    if (jogoFinalizado || travar) return;

    const carta = e.target.closest('.carta');
    if (!carta) return;
    const index = carta.dataset.index;

    if (userId !== turnoAtual) return;
    const cartaData = estadoTabuleiro.cartas[index];
    if (cartaData.virada || cartaData.par) return;

    if (cartasViradasLocal.length === 2) return;

    cartasViradasLocal.push(index);
    // Flip card locally immediately to provide instant feedback to the player
    carta.classList.add('flip');

    if (cartasViradasLocal.length === 2) {
        travar = true;
        fetch('../php/jogar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ indices: cartasViradasLocal, sala_id: salaId })
        })
            .then(res => res.json())
            .then(data => {
            if (data.status === 'ok') {
                turnoAtual = Number(data.turno);
                statusPartida = data.status_partida;

                estadoTabuleiro = data.tabuleiro;
                renderizarTabuleiro();
                mostrarTurno();
                atualizarPlacar();

                if (!data.tabuleiro.cartas[cartasViradasLocal[0]].par && !data.tabuleiro.cartas[cartasViradasLocal[1]].par) {
                    // Não é par, desvira as cartas depois de 1 segundo
                    setTimeout(() => {
                        // Remove a classe 'flip' para virar as cartas para trás
                        cartasViradasLocal.forEach(i => {
                            const c = tabuleiro.children[i].querySelector('.carta-inner');
                            if (c) c.classList.remove('flip');
                        });
                        // Atualiza o estado local removendo temporariamente_virada
                        cartasViradasLocal.forEach(i => {
                            if (estadoTabuleiro.cartas[i]) {
                                delete estadoTabuleiro.cartas[i].temporariamente_virada;
                            }
                        });
                        // Libera o bloqueio para permitir novos cliques
                        travar = false;
                        // Limpa o array de cartas viradas localmente
                        cartasViradasLocal = [];
                        // Atualiza a renderização do tabuleiro
                        renderizarTabuleiro();
                    }, 1000);
                } else {
                    // Par encontrado ou jogo finalizado
                    if (statusPartida === 'finalizada') {
                        jogoFinalizado = true;
                        const tempoFinal = pararCronometro();
                        endGameTime.textContent = 'Tempo: ' + tempoFinal + ' segundos';
                        endGameErrors.textContent = 'Erros: ' + erros;
                        endGameScreen.style.display = 'block';
                        tabuleiro.style.display = 'none';
                        pararPolling();
                    }
                    travar = false;
                    cartasViradasLocal = [];
                }
            } else {
                alert(data.mensagem || 'Erro ao jogar.');
                travar = false;
                cartasViradasLocal = [];
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
    let delay = 0;
    todasCartas.forEach(carta => {
        setTimeout(() => carta.classList.add('flip'), delay);
        delay += 150;
    });
    setTimeout(() => {
        document.querySelectorAll('.carta').forEach(c => c.classList.remove('flip'));
        iniciarCronometro();
        iniciarPolling();
    }, delay + 3000);
}

// ================== Inicialização ==================
atualizarEstado();
iniciarPolling();
