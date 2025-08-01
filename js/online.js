const criarSalaBtn = document.getElementById('criarSalaBtn');
const entrarSalaBtn = document.getElementById('entrarSalaBtn');

if (criarSalaBtn) {
    criarSalaBtn.addEventListener('click', () => {
        fetch('../php/criar_sala.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'ok') {
                    window.location.href = `salaoJogoOnline.php?sala=${data.sala_id}`;
                }
                else {
                    alert(data.message);
                }
            });
    });
}

const salasContainer = document.getElementById("salasContainer");

if (entrarSalaBtn) {
    entrarSalaBtn.addEventListener('click', () => {
        fetch('../php/buscarSalas.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Não foi possível buscar partida!
                    return;
                }

                if (data.length === 0) {
                    // Nenhuma partida encontrada!
                    return;
                }

                salasContainer.innerHTML = ' ';

                const table = document.createElement('table');
                table.classList.add('salas-table');

                // Create header
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Sala', 'Criador'].forEach(text => {
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

    const cells = [
        item.sala_id,
        item.criador,
    ];

    cells.forEach(cellText => {
        const td = document.createElement('td');
        td.textContent = cellText;
        tr.appendChild(td);
    });

    // Add enter button cell
    const enterTd = document.createElement('td');
    const enterBtn = document.createElement('button');
    enterBtn.textContent = 'Entrar';
    enterBtn.classList.add('entrar-btn');
    enterBtn.addEventListener('click', () => {
        entrarSala(item.sala_id);
    });
    enterTd.appendChild(enterBtn);
    tr.appendChild(enterTd);

    tbody.appendChild(tr);
});
                table.appendChild(tbody);

                salasContainer.appendChild(table);
            })
            .catch(error => {
                salasContainer.innerHTML = '<p>Erro ao carregar salas</p>';
                console.error('Erro ao carregar salas:', error);
            });
        salasContainer.style.display = 'block';
    })
}

function entrarSala(salaId) {
    fetch('../php/entrar_sala.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'sala_id=' + salaId
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'ok') {
            window.location.href = 'salaoJogoOnline.php?sala=' + salaId;
        }
        else {
            alert(data.message);
        }
    });
}
