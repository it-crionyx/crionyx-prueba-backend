const db = require('../config/database');
const query = require('../query/dbQuery');

/**
 * Obtiene los documentos con el cruce de tablas y realiza el cálculo de retención
 */
exports.getAllDocumentos = () => {
  return new Promise((resolve, reject) => {
    db.get(query.queryUvt, [], (err, uvtRow) => {
      if (err) return reject(err);

      if (!uvtRow) {
        return reject(new Error('No se encontró el VALOR_UVT en la base de datos'));
      }

      const valorUvt = uvtRow.valor;

      db.all(query.queryDocs, [], (err, rows) => {
        if (err) return reject(err);

        const results = rows.map(row => {
          const valorBasePesos = row.base_uvt * valorUvt;
          const aplicaRetencion = row.valor_total > valorBasePesos;

          return {
            id: row.id,
            nombre_proveedor: row.nombre_proveedor,
            valor_total: row.valor_total,
            valor_base_pesos: valorBasePesos,
            aplica_retencion: aplicaRetencion,
            ya_procesado: !!row.ya_procesado
          };
        });

        resolve(results);
      });
    });
  });
};

/**
 * Inserta o actualiza un documento procesado
 */
exports.procesarDocumento = (data) => {
  const { id_documento, aplica_retencion, valor_base } = data;

  return new Promise((resolve, reject) => {
    db.run(query.queryProcesar, [id_documento, aplica_retencion, valor_base], function (err) {
      if (err) return reject(err);
      resolve({ id_documento });
    });
  });
};
