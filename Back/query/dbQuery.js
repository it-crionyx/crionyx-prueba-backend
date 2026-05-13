const queryUvt = "SELECT valor FROM variables_sistema WHERE clave = 'VALOR_UVT'";

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

const queryProcesar = `
    INSERT INTO documentos_procesados (id_documento, aplica_retencion, valor_base)
    VALUES (?, ?, ?)
    ON CONFLICT(id_documento) DO UPDATE SET
      aplica_retencion = excluded.aplica_retencion,
      valor_base = excluded.valor_base
  `;

module.exports = {
    queryUvt,
    queryDocs,
    queryProcesar
}