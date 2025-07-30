let electroSwing = new Audio('../audio/jemieBerry.mp3');
electroSwing.loop = true;
electroSwing.volume = 0.5;


startGameBtn.addEventListener('click', () => {
    electroSwing.play()
        .then(() => console.log('Música tocando!'))
        .catch(e => console.error('Erro ao reproduzir música:', e));
}, { once: true });
