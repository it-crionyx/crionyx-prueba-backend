DROP TABLE IF EXISTS proveedores;
DROP TABLE IF EXISTS documentos;
DROP TABLE IF EXISTS retenciones;
DROP TABLE IF EXISTS variables_sistema;
DROP TABLE IF EXISTS documentos_procesados;

CREATE TABLE proveedores (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  retencion_id INTEGER
);

CREATE TABLE documentos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  proveedor_id INTEGER,
  valor_total REAL NOT NULL
);

CREATE TABLE retenciones (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  tipo TEXT,
  base_uvt INTEGER
);

CREATE TABLE variables_sistema (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  clave TEXT,
  valor REAL
);

CREATE TABLE documentos_procesados (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  documento_id INTEGER,
  proveedor TEXT,
  valor_total REAL,
  aplica_retencion INTEGER
);

INSERT INTO variables_sistema (clave, valor)
VALUES ('VALOR_UVT', 54000);

INSERT INTO retenciones (tipo, base_uvt) VALUES
('Servicios', 4),
('Compras', 27),
('Honorarios', 0);

INSERT INTO proveedores (nombre, retencion_id) VALUES
('Proveedor A', 1),
('Proveedor B', 2),
('Proveedor C', 3);

INSERT INTO documentos (proveedor_id, valor_total) VALUES
(1, 300000),
(2, 1000000),
(3, 150000);