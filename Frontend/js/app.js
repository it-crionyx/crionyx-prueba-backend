const CONFIG = {
    BASE_URL: '/crionyx-prueba-backend',
    ENDPOINT_GET: '/api/documentos',
    ENDPOINT_POST: '/api/procesar'
};

let loadedDocuments = [];

const elements = {
    body: document.getElementById('tabla-body'),
    checkAll: document.getElementById('check-all'),
    footer: document.getElementById('table-footer'),
    footerInfo: document.getElementById('footer-info'),
    processButton: document.getElementById('btn-procesar'),
    selectedMetric: document.getElementById('metric-seleccionados'),
    toast: document.getElementById('toast')
};

async function loadDocuments() {
    elements.body.innerHTML = '<tr><td colspan="8" class="empty-state">Cargando registros desde la base de datos...</td></tr>';
    elements.checkAll.checked = false;

    try {
        const response = await fetch(`${CONFIG.BASE_URL}${CONFIG.ENDPOINT_GET}`);
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const data = await response.json();
        loadedDocuments = Array.isArray(data) ? data : (data.datos || []);
        renderTable(loadedDocuments);
        calculateMetrics(loadedDocuments);
    } catch (error) {
        console.error('Error al cargar documentos:', error);
        elements.body.innerHTML = `<tr><td colspan="8" class="empty-state error-text"><strong>No fue posible cargar la bandeja.</strong> ${error.message}</td></tr>`;
        elements.footer.hidden = true;
    }
}

function renderTable(records) {
    if (records.length === 0) {
        elements.body.innerHTML = '<tr><td colspan="8" class="empty-state">No hay documentos registrados.</td></tr>';
        elements.footer.hidden = true;
        return;
    }

    elements.body.innerHTML = records.map((document) => {
        const appliesWithholding = document.aplica_retencion == 1 || document.aplica_retencion === true;
        const status = appliesWithholding ? '<span class="badge-estado aplica">APLICA</span>' : '<span class="badge-estado no-aplica">NO APLICA</span>';
        const checkbox = appliesWithholding
            ? `<input type="checkbox" class="checkbox-custom doc-checkbox" data-id="${document.id_documento}">`
            : '<input type="checkbox" class="checkbox-custom" disabled title="No aplica retención">';

        return `<tr class="${appliesWithholding ? 'aplica' : ''}">
            <td class="check-column">${checkbox}</td>
            <td>${document.id_documento}</td>
            <td>${document.nombre_proveedor}</td>
            <td><span class="badge-tipo">${document.tipo_retencion}</span></td>
            <td class="center">${document.base_uvt} UVT</td>
            <td class="cell-money">${formatCurrency(document.base_pesos)}</td>
            <td class="cell-money highlight">${formatCurrency(document.valor_total)}</td>
            <td>${status}</td>
        </tr>`;
    }).join('');

    elements.footer.hidden = false;
    const applicableCount = records.filter((document) => document.aplica_retencion == 1 || document.aplica_retencion === true).length;
    elements.footerInfo.textContent = `${records.length} documentos cargados · ${applicableCount} listos para procesamiento`;
    document.querySelectorAll('.doc-checkbox').forEach((checkbox) => checkbox.addEventListener('change', updateSelectedCount));
}

function calculateMetrics(records) {
    const applicableCount = records.filter((document) => document.aplica_retencion == 1 || document.aplica_retencion === true).length;
    document.getElementById('metric-total').textContent = records.length;
    document.getElementById('metric-aplican').textContent = applicableCount;
    document.getElementById('metric-no-aplican').textContent = records.length - applicableCount;
    elements.selectedMetric.textContent = '0';
    elements.processButton.disabled = true;

    if (records[0]?.valor_uvt_sistema) {
        document.getElementById('uvt-valor').textContent = formatCurrency(records[0].valor_uvt_sistema);
    }
}

function updateSelectedCount() {
    const total = document.querySelectorAll('.doc-checkbox:checked').length;
    elements.selectedMetric.textContent = total;
    elements.processButton.disabled = total === 0;
}

function toggleSelectAll(shouldSelect) {
    document.querySelectorAll('.doc-checkbox').forEach((checkbox) => { checkbox.checked = shouldSelect; });
    updateSelectedCount();
}

async function processSelectedDocuments() {
    const selectedCheckboxes = [...document.querySelectorAll('.doc-checkbox:checked')];
    if (!selectedCheckboxes.length) return;

    const selectedIds = selectedCheckboxes.map((checkbox) => Number(checkbox.dataset.id));
    const documents = loadedDocuments.filter((document) => selectedIds.includes(Number(document.id_documento))).map((document) => ({
        id_documento: Number(document.id_documento), aplica_retencion: 1, base_pesos: Number(document.base_pesos)
    }));

    elements.processButton.disabled = true;
    elements.processButton.textContent = 'Procesando...';
    try {
        const response = await fetch(`${CONFIG.BASE_URL}${CONFIG.ENDPOINT_POST}`, {
            method: 'POST', headers: { 'Content-Type': 'application/json; charset=UTF-8' }, body: JSON.stringify({ documentos: documents })
        });
        const data = await response.json();
        if (!response.ok || data.error) throw new Error(data.error || `Error HTTP: ${response.status}`);
        showToast('Retenciones procesadas correctamente.', 'success');
        setTimeout(loadDocuments, 900);
    } catch (error) {
        showToast(`Error de conexión: ${error.message}`, 'error');
    } finally {
        elements.processButton.textContent = 'Procesar seleccionados →';
        updateSelectedCount();
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
}

function showToast(message, type) {
    elements.toast.textContent = message;
    elements.toast.className = `show ${type}`;
    setTimeout(() => { elements.toast.className = ''; }, 4000);
}

document.getElementById('btn-recargar').addEventListener('click', loadDocuments);
document.getElementById('btn-procesar').addEventListener('click', processSelectedDocuments);
elements.checkAll.addEventListener('change', (event) => toggleSelectAll(event.target.checked));
document.addEventListener('DOMContentLoaded', loadDocuments);
