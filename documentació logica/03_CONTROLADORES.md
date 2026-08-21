# 3. Controladores

## Qué es un controlador

El controlador recibe una petición, revisa los datos, llama al modelo y decide qué respuesta entregar. No debe contener todo el HTML ni consultas SQL extensas.

## Controladores del proyecto

| Controlador | Proceso |
|---|---|
| `controllers/auth/AuthController.php` | Login, logout, bloqueo y recuperación |
| `controllers/FacturaController.php` | POS, procesar factura, clientes AJAX y borrador |
| `controllers/ProductosController.php` | CRUD, imágenes, búsqueda y páginas |
| `controllers/ClientesController.php` | CRUD de clientes |
| `controllers/CategoriaController.php` | CRUD de categorías |
| `controllers/EmpresaController.php` | Datos de la empresa |
| `controllers/ParametrizacionController.php` | Resolución DIAN e impuestos |
| `controllers/RentabilidadController.php` | Reportes de utilidad |
| `controllers/DashboardController.php` | KPIs, gráficos y facturas recientes |
| `controllers/RegisterUsuarioAdmin.php` | Alta de usuarios |
| `controllers/EditUsuarioController.php` | Edición de usuarios |
| `controllers/DeletedUsuarioController.php` | Eliminación de usuarios |

## Acciones por URL

Algunos controladores usan una acción en la URL:

```text
controllers/FacturaController.php?action=init_pos
controllers/FacturaController.php?action=procesar
controllers/FacturaController.php?action=guardar_borrador
```

El controlador lee `$_GET['action']` y llama al método correspondiente.

## Ejemplo de petición de formulario

```text
Formulario -> RegisterUsuarioAdmin.php -> Usuario::registrar() -> INSERT usuarios
```

El controlador debe:

1. Confirmar el método HTTP.
2. Leer `$_POST` o JSON.
3. Limpiar y convertir datos.
4. Validar campos obligatorios.
5. Crear el modelo.
6. Ejecutar el método del modelo.
7. Mostrar una alerta o devolver JSON.

## Respuestas

### Redirección con alerta

Se usa en formularios normales:

```php
$_SESSION['alert'] = [
    'icon' => 'success',
    'title' => 'Guardado',
    'text' => 'La operación fue exitosa.'
];
header('Location: ../views/admin/clientes.php');
exit;
```

### Respuesta JSON

Se usa en AJAX:

```php
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
```

## Seguridad de roles

`views/layouts/header.php` protege las vistas. El administrador tiene acceso completo. El vendedor solo puede entrar a facturación, impresión, clientes y sus ventas.

Un controlador que modifica información también debe confiar en la sesión, no en un `id_usuario` enviado libremente desde el navegador.

## Error frecuente: controlador que no se ejecuta

Revisar:

- La URL y el nombre de `action`.
- El método HTTP enviado.
- Que el archivo tenga el `require_once` correcto.
- Que el método termine con `exit` después de responder.
- Que la respuesta JSON no tenga HTML o warnings antes del JSON.
