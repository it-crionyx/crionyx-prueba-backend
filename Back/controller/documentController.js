const documentService = require('../services/documentServices');

exports.getDocumentos = async (req, res) => {
  try {
    const results = await documentService.getAllDocumentos();
    res.json(results);
  } catch (err) {
    console.error('Error en getDocumentos:', err);
    res.status(500).json({ error: 'Error al obtener documentos' });
  }
};

exports.procesarDocumento = async (req, res) => {
  const { id_documento, aplica_retencion, valor_base } = req.body;

  try {
    const result = await documentService.procesarDocumento({ id_documento, aplica_retencion, valor_base });
    res.json({ message: 'Documento procesado correctamente', id: result.id_documento });
  } catch (err) {
    console.error('Error en procesarDocumento:', err);
    res.status(500).json({ error: 'Error al procesar el documento: ' + err.message });
  }
};
