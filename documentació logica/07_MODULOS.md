# 7. Módulos

## Autenticación

Archivos principales:

- `views/auth/login.php`
- `controllers/auth/AuthController.php`
- `models/Usuario.php`
- `views/layouts/header.php`

Incluye login, logout, bloqueo por intentos, recuperación y timeout de 10 minutos.

## Dashboard

Archivos:

- `views/admin/dashboard.php`
- `controllers/DashboardController.php`

Muestra ventas del día, ventas del mes, stock bajo, clientes, gráfica, productos más vendidos y facturas recientes paginadas.

## Facturación POS

Archivos:

- `views/admin/Facturas.php`
- `public/js/facturas.js`
- `controllers/FacturaController.php`
- `models/Factura.php`
- `models/VentaBorrador.php`

El administrador y el vendedor pueden usar el POS. La venta queda asociada al usuario de la sesión.

## Usuarios y comisiones

Archivos:

- `views/admin/gempleados.php`
- `controllers/RegisterUsuarioAdmin.php`
- `controllers/EditUsuarioController.php`
- `models/Usuario.php`

La comisión se guarda en `usuarios.porcentaje_comision` y se congela en cada factura.

## Productos y categorías

Archivos:

- `views/admin/productos.php`
- `controllers/ProductosController.php`
- `models/Productos.php`
- `views/admin/categorias.php`
- `controllers/CategoriaController.php`
- `models/Categoria.php`

Incluye stock, precios, IVA, imágenes y alerta de pérdida de margen.

## Clientes

Archivos:

- `views/admin/clientes.php`
- `controllers/ClientesController.php`
- `models/Clientes.php`

Admin y vendedor pueden consultar y gestionar clientes según el permiso de la vista.

## Empresa y parametrización

Archivos:

- `views/admin/empresa.php`
- `controllers/EmpresaController.php`
- `models/Empresa.php`
- `views/admin/parametrizacion.php`
- `controllers/ParametrizacionController.php`
- `models/ResolucionDian.php`

Guardan los datos legales, resolución, prefijo, rango e impuestos.

## Rentabilidad

Archivos:

- `views/admin/rentabilidad.php`
- `controllers/RentabilidadController.php`
- `models/Rentabilidad.php`

Calcula ventas, costos, utilidad y márgenes. El historial es global y de administración.

## Mis ventas

Archivo principal: `views/seller/mis_ventas.php`.

Muestra ventas del usuario conectado, filtros de fecha, suma del periodo, comisión estimada y paginación. Nunca debe quitarse el filtro por `id_usuario`.

## Impresión y accesibilidad

- `controllers/FacturaController.php?action=imprimir`
- `views/admin/imprimir_factura.php`
- `window.print()`
- Web Speech API

La DIAN es simulada con fines académicos. No afirmar que existe integración real con DIAN.
