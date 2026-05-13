const API_URL = 'http://localhost:3000/api';
const bodyTable = document.getElementById('documentos-body');
const btnRefresh = document.getElementById('btn-refresh');
const statusMsg = document.getElementById('status-msg');

async function fetchDocumentos() {
    try {
        statusMsg.textContent = 'Cargando...';
        statusMsg.className = 'status-msg';
        
        const response = await fetch(`${API_URL}/documentos`);
        if (!response.ok) throw new Error('Error al conectar con la API');
        
        const data = await response.json();
        renderTable(data);
        
        statusMsg.textContent = 'Datos actualizados';
        statusMsg.classList.add('status-success');
    } catch (error) {
        console.error(error);
        statusMsg.textContent = 'Error: No se pudo conectar al servidor';
        statusMsg.classList.add('status-error');
    }
}

function renderTable(documentos) {
    bodyTable.innerHTML = '';
    
    documentos.forEach(doc => {
        const tr = document.createElement('tr');
        
        // Aplicar clase condicional si aplica retención
        if (doc.aplica_retencion) {
            tr.classList.add('retencion-row');
        }

        tr.innerHTML = `
            <td>#${doc.id}</td>
            <td><strong>${doc.nombre_proveedor}</strong></td>
            <td>$${doc.valor_total.toLocaleString()}</td>
            <td>$${doc.valor_base_pesos.toLocaleString()}</td>
            <td>
                <span class="badge ${doc.aplica_retencion ? 'badge-yes' : 'badge-no'}">
                    ${doc.aplica_retencion ? 'SÍ APLICA' : 'NO APLICA'}
                </span>
            </td>
            <td>
                <button onclick="procesar(${doc.id}, ${doc.aplica_retencion}, ${doc.valor_base_pesos})" 
                        class="btn-secondary" 
                        id="btn-proc-${doc.id}">
                    Procesar
                </button>
            </td>
        `;
        bodyTable.appendChild(tr);
    });
}

async function procesar(id, aplica, base) {
    const btn = document.getElementById(`btn-proc-${id}`);
    const originalText = btn.textContent;
    
    try {
        btn.disabled = true;
        btn.textContent = '...';

        const response = await fetch(`${API_URL}/procesar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_documento: id,
                aplica_retencion: aplica,
                valor_base: base
            })
        });

        if (!response.ok) throw new Error();

        btn.textContent = 'Listo ✅';
        btn.style.background = '#dcfce7';
        btn.style.borderColor = '#86efac';
    } catch (error) {
        btn.disabled = false;
        btn.textContent = 'Reintentar';
        alert('Error al procesar el documento');
    }
}

btnRefresh.addEventListener('click', fetchDocumentos);

// Carga inicial
window.addEventListener('DOMContentLoaded', fetchDocumentos);
