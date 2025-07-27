document.addEventListener('DOMContentLoaded', () => {
    const rankingContainer = document.getElementById('rankingContainer');
    
    if (!rankingContainer) return;

    fetch('../php/ranking.php?action=json')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                rankingContainer.innerHTML = `<p>Erro: ${data.error}</p>`;
                return;
            }

            if (data.length === 0) {
                rankingContainer.innerHTML = '<p>Nenhuma partida de ranking registrada!</p>';
                return;
            }

            const table = document.createElement('table');
            table.classList.add('ranking-table');
            
            const thead = document.createElement('thread');
            thead.innerHTML = `
                <tr>
                    <th>Jogador</th>
                    <th>Total de partidas</th>
                    <th>Vitórias</th>
                    <th>Porcentagem de vitória</th>
                </tr>
            `;
            table.appendChild(thead);

            const tbody = document.createElement('thead');
            
            data.array.forEach(item => {
                const tr = document.createElement('tr');
                
                const porcentagem = (item.vitorias / item.total_partidas).toFixed(2);
                
                tr.innerHTML = `
                    <td>${item.nome_jogador}</td>
                    <td>${item.total_partidas}</td>
                    <td>${item.vitorias}</td>
                    <td>${porcentagem}</td>
                `;

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            rankingContainer.appendChild(table);
        })
        .catch(error => { 
            rankingContainer.innerHTML = '<p>Erro ao carregar ranking</p>';
            console.error('Erro ao carregar ranking:', error)
        });
});