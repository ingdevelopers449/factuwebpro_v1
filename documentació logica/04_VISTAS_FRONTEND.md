# 4. Vistas y FrontEnd

## Qué es una vista

Una vista es una pantalla que el usuario puede abrir. Contiene HTML, PHP para imprimir datos y formularios. No debería ser el lugar principal para guardar reglas de negocio.

## Vistas protegidas

Antes de mostrar contenido privado, las vistas cargan:

```php
require_once '../layouts/header.php';
```

`header.php` revisa sesión, inactividad y rol. También carga el sidebar correcto.

## Vistas de administración

| Vista | Uso |
|---|---|
| `views/admin/dashboard.php` | Indicadores, gráficos y facturas recientes |
| `views/admin/Facturas.php` | Punto de venta |
| `views/admin/clientes.php` | Clientes |
| `views/admin/productos.php` | Productos e imágenes |
| `views/admin/categorias.php` | Categorías |
| `views/admin/empresa.php` | Datos legales |
| `views/admin/gempleados.php` | Usuarios, roles y comisiones |
| `views/admin/parametrizacion.php` | Resolución DIAN |
| `views/admin/rentabilidad.php` | Utilidad e historial global |
| `views/admin/imprimir_factura.php` | Ticket o impresión de factura |

## Vistas del vendedor

`views/seller/mis_ventas.php` muestra únicamente las facturas del usuario de la sesión. El filtro importante está en el modelo:

```sql
WHERE f.id_usuario = ?
```

## Layouts

- `header.php`: sesión, permisos, menú superior y sidebar.
- `footer.php`: scripts globales, SweetAlert2 y Centro de Ayuda.
- `sidebaradmin.php`: menú del administrador.
- `sidebarseller.php`: menú reducido del vendedor.

## FrontEnd del POS

`public/js/facturas.js` se encarga de:

1. Cargar clientes, categorías y productos por AJAX.
2. Guardar productos en el carrito.
3. Cambiar cantidades.
4. Calcular subtotal, IVA y total.
5. Guardar el borrador.
6. Enviar la venta a `FacturaController.php`.
7. Mostrar alertas con SweetAlert2.

## Datos entre PHP y JavaScript

El navegador envía JSON al controlador:

```javascript
fetch('../../controllers/FacturaController.php?action=procesar', {
    method: 'POST',
    body: JSON.stringify(datosVenta)
});
```

El controlador responde JSON. Si la respuesta no es JSON válido, revisar warnings de PHP, `echo` inesperados y errores de conexión.

## Impresión

La impresión usa `window.print()`. No se usa una librería PDF externa. La vista de impresión recibe `$factura` y `$detalles` desde `FacturaController::imprimir()`.

## Escape de texto

Al imprimir datos provenientes de la base de datos en HTML usar:

```php
htmlspecialchars($valor)
```

Esto evita que un texto guardado se convierta en código HTML o JavaScript.

## Diseño

La interfaz usa Bootstrap 5.3, FontAwesome, colores navy/naranja/ámbar y el CSS de `public/css/style.css`. No agregar Tailwind ni cambiar el footer sin necesidad.
