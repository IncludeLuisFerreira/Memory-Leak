// Seleciona o container do tabuleiro
const tabuleiro = document.querySelector('.tabuleiro');

// Lista com as cartas (sem os pares duplicados ainda)
const imagensBase = [
    'img/cartas/freackle.png',
    'img/cartas/mitzi.jpg',
    'img/cartas/mordecai.png',
    'img/cartas/nicomed.jpeg',
    'img/cartas/pepper.png',
    'img/cartas/rocky.jpg',
    'img/cartas/serafine.jpg',
    'img/cartas/viktor.png'
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
            <div class="verso"><img src="img/bkg.png" alt="Verso"></div>
        </div>
    `;

    tabuleiro.appendChild(carta);
});

// Lógica do jogo (comparação e controle)
let primeiraCarta = null;
let segundaCarta = null;
let travar = false;

tabuleiro.addEventListener('click', e => {
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

        if (img1 === img2) {
            // Cartas corretas: mantêm viradas
            primeiraCarta = null;
            segundaCarta = null;
            travar = false;
        } else {
            // Cartas erradas: desvira depois de 1 segundo
            setTimeout(() => {
                primeiraCarta.classList.remove('flip');
                segundaCarta.classList.remove('flip');
                primeiraCarta = null;
                segundaCarta = null;
                travar = false;
            }, 1000);
        }
    }
});
