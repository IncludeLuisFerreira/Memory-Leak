document.addEventListener('DOMContentLoaded', function() {
    const warningScreen = document.getElementById('warningScreen');
    const mainContent = document.getElementById('mainContent');
    const continueBtn = document.getElementById('continueBtn');

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos:', { warningScreen, mainContent, continueBtn });
    
    // Esconder conteúdo principal inicialmente
    if (mainContent) mainContent.style.display = 'none';
    
    // Verificar se o botão existe antes de adicionar o event listener
    if (continueBtn) {
        continueBtn.addEventListener('click', function() {
            console.log('Botão clicado!'); // Debug
            
            // Efeito fade out
            if (warningScreen) {
                warningScreen.style.opacity = '0';
                warningScreen.style.transition = 'opacity 1s ease-out';
            }

            // Mostrar conteúdo principal após fade out
            setTimeout(function() {
                if (warningScreen) warningScreen.style.display = 'none';
                if (mainContent) {
                    mainContent.style.display = 'block';
                    mainContent.style.opacity = '0';
                    
                    // Fade in do conteúdo principal
                    setTimeout(() => {
                        if (mainContent) mainContent.style.opacity = '1';
                    }, 10);
                }
                
                // Adicionar música de jazz (opcional)
                try {
                    const jazzMusic = new Audio('https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3');
                    jazzMusic.loop = true;
                    jazzMusic.volume = 0.3;
                    
                    document.addEventListener('click', function startMusic() {
                        jazzMusic.play().catch(e => console.error('Erro ao reproduzir música:', e));
                        document.removeEventListener('click', startMusic);
                    }, { once: true });
                } catch (e) {
                    console.error('Erro ao carregar música:', e);
                }
            }, 1000);
        });
    } else {
        console.error('Botão continueBtn não encontrado!');
    }
});