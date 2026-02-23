document.addEventListener('DOMContentLoaded', () => {
    const rankingContainer = document.getElementById('rankingContainer');
    
    if (!rankingContainer) return;

    fetch('/api/ranking')
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

            // Clear previous content
            rankingContainer.innerHTML = '';

            // Create table
            const table = document.createElement('table');
            table.classList.add('ranking-table');

            // Create header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            ['Jogador', 'Total de partidas', 'Vitórias', 'Porcentagem de vitória'].forEach(text => {
                const th = document.createElement('th');
                th.textContent = text;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create body
            const tbody = document.createElement('tbody');
            data.forEach(item => {
                const tr = document.createElement('tr');

                const porcentagem = item.total_partidas > 0 ? ((item.vitorias / item.total_partidas) * 100).toFixed(2) + '%' : '0%';

                const cells = [
                    item.nome,
                    item.total_partidas,
                    item.vitorias,
                    porcentagem
                ];

                cells.forEach(cellText => {
                    const td = document.createElement('td');
                    td.textContent = cellText;
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });
            table.appendChild(tbody);

            rankingContainer.appendChild(table);
        })
        .catch(error => {
            rankingContainer.innerHTML = '<p>Erro ao carregar ranking</p>';
            console.error('Erro ao carregar ranking:', error);
        });
});
