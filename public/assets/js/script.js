document.addEventListener('DOMContentLoaded', function() {
    const warningScreen = document.getElementById('warningScreen');
    const mainContent = document.getElementById('mainContent');
    const continueBtn = document.getElementById('continueBtn');
    let jazzMusic;

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos:', { warningScreen, mainContent, continueBtn });
    
    // Esconder conteúdo principal inicialmente
    if (mainContent) mainContent.style.display = 'none';
    
    // Pré-carregar a música
    try {
        jazzMusic = new Audio('../audio/cheerful-promenade-easy-going-electro-swing-composition-for-blogs-149595.mp3');
        jazzMusic.loop = true;
        jazzMusic.volume = 0.3;

        // Verificar se a música está pronta para tocar
        jazzMusic.addEventListener('canplaythrough', function() {
            console.log('Música carregada e pronta para tocar.');
        });
    } catch (e) {
        console.error('Erro ao carregar música:', e);
    }

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
                
                // Tocar música se estiver carregada
                if (jazzMusic) {
                    jazzMusic.play().catch(e => console.error('Erro ao reproduzir música:', e));
                }
            }, 1000);
        });
    } else {
        console.error('Botão continueBtn não encontrado!');
    }
});
