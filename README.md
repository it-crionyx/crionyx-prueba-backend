# crionyx-prueba-backend
Prueba ingreso ingenieria


# 🚀 Prueba Técnica: Ingeniero de Automatización Contable (IA/Backend)

Bienvenido al proceso de selección de **Crionyx**. Esta prueba evalúa tu capacidad para estructurar soluciones escalables, manejar lógica de negocio contable, garantizar la seguridad de los datos y gestionar el control de versiones.

## 📌 Contexto del Proyecto
Crionyx está automatizando la gestión de retenciones para documentos contables. El sistema debe decidir si un documento (factura) requiere una retención basándose en el tipo de proveedor y el valor actual de la **UVT (Unidad de Valor Tributario)**.

### Reglas de Negocio
1. **Base de Retención:** Cada tipo de retención tiene una base mínima expresada en UVTs.
2. **Cálculo de Base en Pesos:** `Valor Base ($) = Base UVT * VALOR_UVT (actual)`.
3. **Validación:** Si el `valor_total` del documento es **mayor** al `Valor Base ($)`, se debe marcar para procesar la retención.
4. **Valor Actual UVT:** El valor para este ejercicio es de **$54,000**.

---

## 🧠 Recursividad y Adaptabilidad (Uso de IA)
El entorno local está configurado para la ejecución nativa de **PHP**. 
Si tu lenguaje principal es Node.js u otro, **se espera que utilices las herramientas de Inteligencia Artificial permitidas (ChatGPT, Gemini, etc.) como puente de traducción de sintaxis.** En Crionyx valoramos tu capacidad para entender la estructura técnica del problema, el flujo de los datos y cómo se conectan los componentes lógicos a la base de datos, por encima de la memorización de un lenguaje específico. Demuestra tu recursividad resolviendo el requerimiento con el stack proporcionado.

## 🛠️ Requerimientos Técnicos

### 1. Backend (PHP)
* **API REST:** Crea un endpoint `GET /api/documentos` que:
    * Cruce las tablas `documentos`, `proveedores` y `retenciones`.
    * Calcule dinámicamente el valor base en pesos usando la tabla `variables_sistema`.
    * Retorne un JSON con el ID del documento, nombre del proveedor, valor total, y si aplica o no la retención.
* **Persistencia:** Crea un endpoint `POST /api/procesar` que inserte los resultados en la tabla `documentos_procesados`.
* **Seguridad:** Es **obligatorio** el uso de **consultas preparadas (Prepared Statements)**.
* **Configuración:** La conexión a la base de datos SQLite debe leerse desde un archivo `.env`.

### 2. Frontend (HTML + JS + CSS)
* Desarrolla una interfaz simple que consuma el endpoint de documentos.
* Muestra los resultados en una tabla.
* **Estilo condicional:** Las filas de documentos que **SÍ** aplican para retención deben resaltarse (ej. fondo verde suave o una etiqueta distintiva).

---

## 🗄️ Estructura de la Base de Datos (SQLite)
El repositorio incluye un archivo `database.sqlite` con la siguiente estructura:
* `proveedores`: Datos básicos y relación con su retención.
* `documentos`: Facturas emitidas por los proveedores.
* `retenciones`: Definición de bases en UVT (Servicios: 4, Compras: 27, Honorarios: 0).
* `variables_sistema`: Almacena el `VALOR_UVT`.
* `documentos_procesados`: Tabla de destino para los resultados.

---

## ⚙️ Instrucciones de Entrega (Git Flow)
1. **Branching:** Crea una rama llamada `dev/nombre-apellido` para trabajar.
2. **Commits:** Realiza commits frecuentes y descriptivos.

---
**Nota:** El uso de IA (ChatGPT, etc.) está permitido como herramienta de apoyo, pero la arquitectura y la explicación del código durante la entrevista técnica posterior serán definitivas.
