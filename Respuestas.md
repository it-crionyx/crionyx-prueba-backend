# Respuestas

## Arquitectura del proyecto

El proyecto usa una arquitectura por capas, con un frontend desacoplado de una API REST en PHP y una base de datos SQLite.

- **Presentación:** `Frontend/index.html`, `Frontend/css/index.css` y `Frontend/js/app.js`. son los archivos que muestran la información, consumen la API y permite seleccionar y procesar documentos.
- **Enrutamiento:** el enrutamiento ocurre en `Backend/Routes/Api.php`. donde se recibe la URL y el método HTTP como parametros, dados por el frontend y dirige cada solicitud al controlador correspondiente.
- **Aplicación:** `Backend/controllers/DocumentController.php` y `ProcessController.php`. Valida las solicitudes, coordina los casos de uso y construye las respuestas JSON.
- **Datos y dominio:** `Backend/Model/DocumentModel.php` consulta documentos, proveedores, retenciones y variables del sistema; calcula la base en pesos y determina si aplica la retención. `ProcessModel.php` persiste los documentos procesados mediante una transacción.
- **Infraestructura:** `Backend/config/database.php` crea y configura la conexión PDO con SQLite usando la configuración del archivo `.env`.

El flujo de conexión es: el frontend envía una petición HTTP al enrutador; el enrutador selecciona un controlador; el controlador solicita la operación al modelo; el modelo usa la conexión PDO para consultar o modificar SQLite; finalmente, el controlador devuelve JSON al frontend. Para procesar documentos, el flujo inverso recibe el JSON seleccionado, valida sus columnas, ejecuta el lote en una transacción y responde con el resultado.

## Comandos Linux

Buscar archivos `.log` dentro de `/var/log` que contengan la palabra `error`, sin distinguir mayúsculas y minúsculas:

```bash
find /var/log -type f -name '*.log' -exec grep -il -- 'error' {} +
```

Enviar por SSH cada archivo encontrado a `/tmp/` en el servidor remoto:

```bash
find /var/log -type f -name '*.log' -exec grep -Il -- 'error' {} + -exec scp {} usuario@192.168.1.10:/tmp/ \;
```

El comando SSH solicitará autenticación para `usuario` y copiará únicamente los archivos que contienen `error`.

## Ejecución del proyecto

### 1. Clonar el repositorio

Reemplaza `<URL_DEL_REPOSITORIO>` por la URL real del repositorio:

```bash
git clone <URL_DEL_REPOSITORIO>
cd crionyx-prueba-backend-main
```

Si el repositorio fue clonado dentro de una carpeta con el mismo nombre, entra al directorio que contiene `index.php`, `Backend/`, `Frontend/` y `database.sqlite`.

### 2. Configurar el entorno



Verifica que `.env` tenga la ruta de la base de datos:

```dotenv
DB_PATH=database.sqlite
```

### 3. Verificar PHP

```bash
php -v
php -m | grep -i sqlite
```

PHP debe tener habilitada la extensión `pdo_sqlite`.

### 4. Ejecutar el proyecto

Desde la carpeta raíz del proyecto, donde se encuentra `index.php`, ejecuta:

```bash
php -S 127.0.0.1:8091 index.php
```

No cierres esa terminal mientras uses la aplicación.

### 5. Abrir y probar

- Frontend: `http://127.0.0.1:8091/`
- Consultar documentos: `GET http://127.0.0.1:8091/api/documentos`
- Procesar documentos: `POST http://127.0.0.1:8091/api/procesar`

Para detener el servidor presiona `Ctrl + C`.

## Pruebas en Postman

### GET `/api/documentos`

Configura una petición con:

```text
GET http://127.0.0.1:8091/api/documentos
```

### POST `/api/procesar`

Configura una petición con:

```text
POST http://127.0.0.1:8091/api/procesar
```

En **Headers** agrega:

```text
Content-Type: application/json
```

En **Body > raw > JSON** utiliza un payload como este:

```json
{
	"documentos": [
		{
			"id_documento": 1,
			"aplica_retencion": 1,
			"base_pesos": 216000
		}
	]
}
```

El `id_documento` debe existir y no haber sido procesado previamente. Una respuesta exitosa tiene esta estructura:

```json
{
	"success": true,
	"mensaje": "Documentos procesados exitosamente.",
	"detalle": {
		"procesados": 1
	}
}
```


