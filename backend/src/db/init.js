<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentos Crionyx</title>

  <style>
    :root {
      --bg: #f3f4f6;
      --dark: #111827;
      --primary: #1f2937;
      --ok: #16a34a;
      --no: #6b7280;
      --row: #d4edda;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: var(--bg);
    }

    header {
      background: var(--primary);
      color: white;
      padding: 15px;
      text-align: center;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      padding: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow-x: auto;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      opacity: 0.9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background: #f9fafb;
    }

    tr:hover {
      background: #f1f5f9;
    }

    .retencion {
      background: var(--row);
    }

    .badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      color: white;
    }

    .badge.ok {
      background: var(--ok);
    }

    .badge.no {
      background: var(--no);
    }

    .loading {
      text-align: center;
      padding: 15px;
      color: #555;
    }

    @media (max-width: 600px) {
      th, td {
        font-size: 12px;
        padding: 8px;
      }
    }
  </style>
</head>

<body>

<header>
  <h2>📊 Sistema de Documentos Crionyx</h2>
</header>

<div class="container">

  <div class="topbar">
    <h3>Listado de Documentos</h3>
    <button onclick="cargar()">🔄 Recargar</button>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Proveedor</th>
          <th>Valor Total</th>
          <th>Base UVT</th>
          <th>Valor UVT</th>
          <th>Retención</th>
        </tr>
      </thead>

      <tbody id="tabla"></tbody>
    </table>
  </div>

</div>

<script>
const CONFIG = {
  API_URL: "http://localhost:3000"
};

function formatMoney(value) {
  return "$" + Number(value).toLocaleString("es-CO");
}

async function cargar() {
  const tbody = document.getElementById("tabla");

  tbody.innerHTML = `
    <tr>
      <td colspan="6" class="loading">Cargando datos...</td>
    </tr>
  `;

  try {
    const res = await fetch(`${CONFIG.API_URL}/api/documentos`);
    const json = await res.json();

    tbody.innerHTML = "";

    json.data.forEach(doc => {
      const tr = document.createElement("tr");

      if (doc.aplica_retencion) {
        tr.classList.add("retencion");
      }

      tr.innerHTML = `
        <td>${doc.id}</td>
        <td>${doc.proveedor}</td>
        <td>${formatMoney(doc.valor_total)}</td>
        <td>${doc.base_uvt}</td>
        <td>${formatMoney(doc.valor_uvt)}</td>
        <td>
          <span class="badge ${doc.aplica_retencion ? 'ok' : 'no'}">
            ${doc.aplica_retencion ? 'SI' : 'NO'}
          </span>
        </td>
      `;

      tbody.appendChild(tr);
    });

  } catch (error) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="loading">Error cargando datos</td>
      </tr>
    `;
    console.error(error);
  }
}

cargar();



//refres page

let loading = false;

async function cargar() {
  if (loading) return;

  loading = true;

  const tbody = document.getElementById("tabla");

  tbody.innerHTML = `
    <tr>
      <td colspan="6" class="loading">Cargando datos...</td>
    </tr>
  `;

  try {
    const res = await fetch(`${CONFIG.API_URL}/api/documentos`);
    const json = await res.json();

    tbody.innerHTML = "";

    json.data.forEach(doc => {
      const tr = document.createElement("tr");

      if (doc.aplica_retencion) {
        tr.classList.add("retencion");
      }

      tr.innerHTML = `
        <td>${doc.id}</td>
        <td>${doc.proveedor}</td>
        <td>${formatMoney(doc.valor_total)}</td>
        <td>${doc.base_uvt}</td>
        <td>${formatMoney(doc.valor_uvt)}</td>
        <td>
          <span class="badge ${doc.aplica_retencion ? 'ok' : 'no'}">
            ${doc.aplica_retencion ? 'SI' : 'NO'}
          </span>
        </td>
      `;

      tbody.appendChild(tr);
    });

  } catch (error) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="loading">Error cargando datos</td>
      </tr>
    `;
    console.error(error);
  } finally {
    loading = false;
  }
}
</script>

</body>
</html>