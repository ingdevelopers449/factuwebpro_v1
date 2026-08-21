# 1. Modelos

## Qué es un modelo

Un modelo es una clase PHP que representa una parte de los datos del sistema. Su trabajo principal es hablar con MySQL y devolver información útil al controlador o a una vista.

Ejemplo sencillo:

```php
$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorEmail($email);
```

La vista no debería escribir consultas SQL directamente. La consulta debe vivir en el modelo.

## Modelos actuales

| Modelo | Clase | Datos o proceso |
|---|---|---|
| `models/Usuario.php` | `Usuario` | Login, usuarios, bloqueo, recuperación y comisión |
| `models/Factura.php` | `Factura` | Crear factura, detalles, impresión, ventas y paginación |
| `models/Productos.php` | `Producto` | Productos, búsqueda, stock, imágenes y paginación |
| `models/Clientes.php` | `Cliente` | Clientes, búsqueda, alta, edición y eliminación |
| `models/Categoria.php` | `Categoria` | Categorías de productos |
| `models/Empresa.php` | `Empresa` | Datos legales de la empresa |
| `models/Rentabilidad.php` | `Rentabilidad` | Utilidad, costos e historial global |
| `models/ResolucionDian.php` | `ResolucionDian` | Resoluciones y consecutivos DIAN simulados |
| `models/Rol.php` | `Rol` | Roles disponibles |
| `models/VentaBorrador.php` | `VentaBorrador` | Guardar y limpiar carritos pendientes |

## Patrón de conexión

Cada modelo incluye `config/database.php`. La conexión global se llama `$conn` y se guarda dentro de la clase:

```php
private \mysqli $conn;

public function __construct()
{
    global $conn;
    $this->conn = $conn;
}
```

## Consultas seguras

Cuando una consulta recibe información del usuario, se debe usar:

```php
$stmt = $this->conn->prepare('SELECT * FROM clientes WHERE id_cliente = ?');
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
```

La letra de `bind_param()` indica el tipo:

- `i`: número entero.
- `d`: número decimal.
- `s`: texto.
- `b`: datos binarios.

## Crear una factura

`Factura::crearFactura()` es el proceso más importante del negocio:

1. Inicia una transacción.
2. Busca una resolución DIAN activa.
3. Obtiene la comisión del usuario.
4. Revisa el documento del cliente.
5. Genera CUFE y QR simulados.
6. Inserta la factura.
7. Inserta cada detalle.
8. Descuenta el stock.
9. Aumenta el consecutivo.
10. Confirma todo o revierte todo si ocurre un error.

## Modelo y paginación

Para una lista grande no se deben traer todos los registros. Se usan:

```sql
LIMIT ? OFFSET ?
```

El modelo debe recibir página y cantidad por página. También debe existir una consulta `COUNT(*)` para saber cuántas páginas mostrar.

## Errores comunes en modelos

- Escribir el nombre de una columna diferente al de la base de datos.
- Olvidar incluir un campo en el `INSERT`.
- Usar `query()` con datos recibidos del usuario.
- No revisar si `prepare()` devolvió `false`.
- No cerrar o reutilizar correctamente los statements.
- Calcular un total usando solo los registros de una página.
