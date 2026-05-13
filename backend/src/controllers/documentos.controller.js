const { obtenerDocumentos } = require("../services/documentos.service");

// GET /api/documentos
const getDocumentos = async (req, res) => {
  try {
    const documentos = await obtenerDocumentos();

    res.json({
      success: true,
      data: documentos
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
};

module.exports = {
  getDocumentos
};