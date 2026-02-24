// Seleciona o container do tabuleiro
const tabuleiro = document.querySelector('.tabuleiro');

// Lista com as cartas (sem os pares duplicados ainda)
const imagensBase = [
    '/assets/img/cartas/freackle.png',
    '/assets/img/cartas/mitzi.jpg',
    '/assets/img/cartas/mordecai.png',
    '/assets/img/cartas/nicomed.jpeg',
    '/assets/img/cartas/pepper.png',
    '/assets/img/cartas/rocky.jpg',
    '/assets/img/cartas/serafine.jpg',
    '/assets/img/cartas/viktor.png'
];

// Duplica para criar pares
let cartas = [...imagensBase, ...imagensBase];

// Função para embaralhar (Fisher-Yates)
function embaralhar(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

// Embaralha as cartas
embaralhar(cartas);

// Cria as cartas no tabuleiro dinamicamente
cartas.forEach(img => {
    const carta = document.createElement('div');
    carta.classList.add('carta');

    carta.innerHTML = `
        <div class="carta-inner">
            <div class="frente"><img src="${img}" alt="Carta"></div>
            <div class="verso"><img src="/assets/img/cartas/verso.jpg" alt="Verso"></div>
        </div>
    `;

    tabuleiro.appendChild(carta);
});

// Lógica do jogo (comparação e controle)
let primeiraCarta = null;
let segundaCarta = null;
let travar = false;

let erros = 0;

let tempoInicio = null;
let timerInterval = null;
let jogoIniciado = false;

const startGameBtn = document.getElementById('startGameBtn');
const timerDisplay = document.getElementById('timerDisplay');

// Função para iniciar o cronômetro
function iniciarCronometro() {
    if (tempoInicio) return; // evita reiniciar
    tempoInicio = Date.now();
    timerInterval = setInterval(() => {
        const tempoDecorrido = Math.floor((Date.now() - tempoInicio) / 1000);
        timerDisplay.textContent = 'Tempo: ' + tempoDecorrido + ' segundos';
    }, 1000);
}

// Função para parar o cronômetro
function pararCronometro() {
    clearInterval(timerInterval);
    const tempoFinal = Math.floor((Date.now() - tempoInicio) / 1000);
    return tempoFinal;
}

// Função para verificar se o jogo terminou
function verificarFimDeJogo() {
    const cartasViradas = document.querySelectorAll('.carta.flip').length;
    return cartasViradas === cartas.length;
}

// Função para virar todas as cartas para mostrar a ordem
function virarTodasCartas() {
    const todasCartas = document.querySelectorAll('.carta');
    todasCartas.forEach(carta => carta.classList.add('flip'));
}

// Função para desvirar todas as cartas
function desvirarTodasCartas() {
    const todasCartas = document.querySelectorAll('.carta');
    todasCartas.forEach(carta => carta.classList.remove('flip'));
}

// Função para iniciar o jogo
function iniciarJogo() {
    if (jogoIniciado) return;
    jogoIniciado = true;

    // Esconde o botão de começar
    startGameBtn.style.display = 'none';

    // Rola a página para centralizar o tabuleiro
    document.querySelector('.tabuleiro').scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Mostra as cartas uma a uma com animação
    const todasCartas = document.querySelectorAll('.carta');
    let delay = 0;
    todasCartas.forEach((carta) => {
        setTimeout(() => {
            carta.classList.add('flip');
        }, delay);
        delay += 150; // 150ms entre cada carta
    });

    // Depois de mostrar todas as cartas, desvira todas de uma vez
    setTimeout(() => {
        todasCartas.forEach(carta => carta.classList.remove('flip'));
        iniciarCronometro();

        // Trava o scroll da página
        document.body.style.overflow = 'hidden';
    }, delay + 500); // espera o tempo total de virar + 0.5s antes de desvirar
}

startGameBtn.addEventListener('click', iniciarJogo);

const restartGameBtn = document.getElementById('restartGameBtn');
restartGameBtn.addEventListener('click', () => {
    // Reinicia o jogo recarregando a página
    window.location.reload();
});

tabuleiro.addEventListener('click', e => {
    if (!jogoIniciado) return;

    const carta = e.target.closest('.carta');

    // Verifica se clicou em uma carta válida
    if (!carta || travar || carta.classList.contains('flip')) return;

    carta.classList.add('flip');

    if (!primeiraCarta) {
        primeiraCarta = carta;
    } else {
        segundaCarta = carta;
        travar = true;

        const img1 = primeiraCarta.querySelector('.frente img').src;
        const img2 = segundaCarta.querySelector('.frente img').src;


        // Envia requisição AJAX para o PHP comparar as cartas
        fetch('/api/compare', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `img1=${encodeURIComponent(img1)}&img2=${encodeURIComponent(img2)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.match) {
                // Cartas corretas: mantêm viradas
                primeiraCarta = null;
                segundaCarta = null;
                travar = false;

                // Verifica se o jogo terminou
                if (verificarFimDeJogo()) {
                    const tempoFinal = pararCronometro();

                    // Animação de fim de jogo
                    const todasCartas = document.querySelectorAll('.carta');
                    todasCartas.forEach(carta => carta.classList.add('fimDoJogo'));

                    // Mostra tela de final
                    const endGameScreen = document.getElementById('endGameScreen');
                    const endGameTime = document.getElementById('endGameTime');
                    const endGameErrors = document.getElementById('endGameErrors');
                    endGameTime.textContent = 'Tempo: ' + tempoFinal + ' segundos';
                    endGameErrors.textContent = 'Erros: ' + erros;
                    endGameScreen.style.display = 'block';

                    // Esconde o tabuleiro e outros elementos
                    document.querySelector('.game-container').style.display = 'none';

                    const pontos = 1000 - tempoFinal * 5 - erros * 2;

                    fetch('/api/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `tempo=${tempoFinal}&modo=1&vencedor=1&pontos=${pontos}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Resultado do salvamento
                    })
                    .catch(error => {
                        console.error('Erro ao salvar partida:', error);
                    });
                }
            } else {
                erros++;
                // Cartas erradas: desvira depois de 1 segundo
                setTimeout(() => {
                    primeiraCarta.classList.remove('flip');
                    segundaCarta.classList.remove('flip');
                    primeiraCarta = null;
                    segundaCarta = null;
                    travar = false;
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            // Em caso de erro, desvira as cartas para evitar travamento
            setTimeout(() => {
                primeiraCarta.classList.remove('flip');
                segundaCarta.classList.remove('flip');
                primeiraCarta = null;
                segundaCarta = null;
                travar = false;
            }, 1000);
        });
    }
});
