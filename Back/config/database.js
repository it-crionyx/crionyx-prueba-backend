const sqlite3 = require('sqlite3').verbose();


// Conectar o crear la base de datos
const db = new sqlite3.Database('./database.sqlite', (err) => {
  if (err) {
    console.error('Error al conectar a la base de datos', err.message);
  } else {
    console.log('Conectado a SQLite');
  }
});