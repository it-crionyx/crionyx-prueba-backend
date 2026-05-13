require('dotenv').config();
const sqlite3 = require('sqlite3').verbose();
const path = require('path');

// El path puede ser relativo a la raíz del proyecto o absoluto
const dbPath = path.resolve(__dirname, '..', process.env.DB_PATH || './database.sqlite');

// Conectar o crear la base de datos
const db = new sqlite3.Database(dbPath, (err) => {
  if (err) {
    console.error('Error al conectar a la base de datos', err.message);
  } else {
    console.log(`Conectado a SQLite en: ${dbPath}`);
  }
});

module.exports = db;