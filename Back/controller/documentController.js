const db = require('../config/database');

exports.getDocumentos = (req, res) => {
  // Primero obtenemos el valor de la UVT
  const queryUvt = "SELECT valor FROM variables_sistema WHERE clave = 'VALOR_UVT'";

  db.get(queryUvt, [], (err, uvtRow) => {
    if (err) {
      return res.status(500).json({ error: 'Error al obtener UVT' });
    }

    const valorUvt = uvtRow ? uvtRow.valor : 54000;

    const queryDocs = `
      SELECT 
        d.id, 
        p.nombre AS nombre_proveedor, 
        d.valor_total, 
        r.base_uvt
      FROM documentos d
      JOIN proveedores p ON d.id_proveedor = p.id
      JOIN retenciones r ON p.id_retencion_aplicable = r.id
    `;

    db.all(queryDocs, [], (err, rows) => {
      if (err) {
        return res.status(500).json({ error: 'Error al obtener documentos' });
      }

      const results = rows.map(row => {
        const valorBasePesos = row.base_uvt * valorUvt;
        const aplicaRetencion = row.valor_total > valorBasePesos;

        return {
          id: row.id,
          nombre_proveedor: row.nombre_proveedor,
          valor_total: row.valor_total,
          valor_base_pesos: valorBasePesos,
          aplica_retencion: aplicaRetencion
        };
      });

      res.json(results);
    });
  });
};

exports.procesarDocumento = (req, res) => {
  const { id_documento, aplica_retencion, valor_base } = req.body;

  if (id_documento === undefined || aplica_retencion === undefined || valor_base === undefined) {
    return res.status(400).json({ error: 'Faltan datos obligatorios (id_documento, aplica_retencion, valor_base)' });
  }

  const sql = `
    INSERT INTO documentos_procesados (id_documento, aplica_retencion, valor_base)
    VALUES (?, ?, ?)
    ON CONFLICT(id_documento) DO UPDATE SET
      aplica_retencion = excluded.aplica_retencion,
      valor_base = excluded.valor_base
  `;

  db.run(sql, [id_documento, aplica_retencion, valor_base], function(err) {
    if (err) {
      return res.status(500).json({ error: 'Error al procesar el documento: ' + err.message });
    }
    res.json({ message: 'Documento procesado correctamente', id: id_documento });
  });
};
