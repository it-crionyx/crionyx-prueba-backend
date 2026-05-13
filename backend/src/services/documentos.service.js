const db = require("../config/db");

const obtenerDocumentos = () => {

  return new Promise((resolve, reject) => {

    const query = `
                SELECT
            d.id,
            p.nombre AS proveedor,
            d.valor_total,
            r.base_uvt,
            vs.valor AS valor_uvt,
            (r.base_uvt * vs.valor) AS base_pesos,
            CASE
                WHEN d.valor_total > (r.base_uvt * vs.valor)
                THEN 1
                ELSE 0
            END AS aplica_retencion
            FROM documentos d
            INNER JOIN proveedores p ON d.proveedor_id = p.id
            INNER JOIN retenciones r ON p.retencion_id = r.id
            INNER JOIN variables_sistema vs ON vs.clave = 'VALOR_UVT'
    `;

    db.all(query, [], (err, rows) => {
      if (err) {
        reject(err);
        return;
      }

      resolve(rows);
    });

  });

};

module.exports = {
  obtenerDocumentos
};