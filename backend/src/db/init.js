const fs = require("fs");
const sqlite3 = require("sqlite3").verbose();
const path = require("path");

const db = new sqlite3.Database(
  path.resolve(__dirname, "../../database.sqlite")
);

const sql = fs.readFileSync(
  path.join(__dirname, "init.sql"),
  "utf-8"
);

db.exec(sql, (err) => {
  if (err) {
    console.error("Error:", err.message);
  } else {
    console.log("Base de datos creada correctamente");
  }
  db.close();
});