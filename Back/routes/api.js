const express = require('express');
const router = express.Router();
const documentController = require('../controller/documentController');
const documentValidator = require('../validators/documentValidator');

// Obtener documentos con cruce de tablas y cálculo de retención
router.get('/documentos', documentController.getDocumentos);

// Procesar documento (insertar en documentos_procesados) con validación
router.post('/procesar', documentValidator.validateProcesar, documentController.procesarDocumento);

module.exports = router;
