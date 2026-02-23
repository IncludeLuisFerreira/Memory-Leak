document.addEventListener('DOMContentLoaded', () => {
    const historicoContainer = document.getElementById('historicoContainer');

    if (!historicoContainer) return;

    fetch('/api/historico')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                historicoContainer.innerHTML = '<p>Erro: ' + data.error + '</p>';
                return;
            }

            if (data.length === 0) {
                historicoContainer.innerHTML = '<p>Nenhum histórico de partidas encontrado.</p>';
                return;
            }

            const table = document.createElement('table');
            table.classList.add('historico-table');

            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr>
                    <th>Data</th>
                    <th>Modo</th>
                    <th>Resultado</th>
                    <th>Pontos</th>
                    <th>Tempo (s)</th>
                </tr>
            `;
            table.appendChild(thead);

            const tbody = document.createElement('tbody');

            data.forEach(item => {
                const tr = document.createElement('tr');

                const dataPartida = new Date(item.data);
                const dataFormatada = dataPartida.toLocaleDateString() + ' ' + dataPartida.toLocaleTimeString();

                const modoTexto = item.modo == 1 ? 'Solo' : (item.modo == 2 ? 'Online' : 'Desconhecido');
                const resultadoTexto = item.vencedor == 1 ? 'Ganhou' : 'Perdeu';

                tr.innerHTML = `
                    <td>${dataFormatada}</td>
                    <td>${modoTexto}</td>
                    <td>${resultadoTexto}</td>
                    <td>${item.pontos}</td>
                    <td>${item.tempo}</td>
                `;

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            historicoContainer.appendChild(table);
        })
        .catch(error => {
            historicoContainer.innerHTML = '<p>Erro ao carregar histórico.</p>';
            console.error('Erro ao carregar histórico:', error);
        });
});
