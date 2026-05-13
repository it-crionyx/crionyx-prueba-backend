const sqlite3 = require("sqlite3").verbose();
require("dotenv").config();

const db = new sqlite3.Database(process.env.DB_PATH, (err) => {
  if (err) {
    console.error("Error conectando DB:", err.message);
  } else {
    console.log("SQLite conectado");
  }
});

module.exports = db;