const express = require('express');
const cors = require('cors');
const apiRoutes = require('./routes/api');
const path = require('path');

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use('/Front', express.static(path.join(__dirname, '../Front')));

// Rutas
app.use('/api', apiRoutes);

// Manejo de errores básico
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: 'Algo salió mal en el servidor' });
});

module.exports = app;
