const express = require("express");

const router = express.Router();

const controller = require("../controllers/documentos.controller");

router.get("/documentos", controller.getDocumentos);

module.exports = router;