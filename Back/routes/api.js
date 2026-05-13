const express = require('express');
const router = express.Router();
const documentController = require('../controller/documentController');

// Obtener documentos con cruce de tablas y cálculo de retención
router.get('/documentos', documentController.getDocumentos);

// Procesar documento (insertar en documentos_procesados)
router.post('/procesar', documentController.procesarDocumento);

module.exports = router;
