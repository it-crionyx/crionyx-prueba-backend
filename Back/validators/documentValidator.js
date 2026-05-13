/**
 * Validador para el endpoint de procesamiento de documentos
 */
exports.validateProcesar = (req, res, next) => {
  const { id_documento, aplica_retencion, valor_base } = req.body;
  const errors = [];

  // Validar id_documento
  if (id_documento === undefined || id_documento === null) {
    errors.push('El campo id_documento es obligatorio');
  } else if (!Number.isInteger(id_documento)) {
    errors.push('id_documento debe ser un número entero');
  }

  // Validar aplica_retencion
  if (aplica_retencion === undefined || aplica_retencion === null) {
    errors.push('El campo aplica_retencion es obligatorio');
  } else if (typeof aplica_retencion !== 'boolean') {
    errors.push('aplica_retencion debe ser un valor booleano');
  }

  // Validar valor_base
  if (valor_base === undefined || valor_base === null) {
    errors.push('El campo valor_base es obligatorio');
  } else if (typeof valor_base !== 'number') {
    errors.push('valor_base debe ser un valor numérico');
  }

  if (errors.length > 0) {
    return res.status(400).json({ errors });
  }

  next();
};
