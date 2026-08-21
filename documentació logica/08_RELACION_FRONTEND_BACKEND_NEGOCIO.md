# 8. Relación FrontEnd, BackEnd y lógica de negocio

## Las tres partes

### FrontEnd

Es lo que ve y usa la persona:

- HTML y PHP de las vistas.
- Bootstrap y CSS.
- Botones, formularios, tablas y modales.
- `public/js/facturas.js`.

### BackEnd

Es lo que ocurre en el servidor:

- Controladores PHP.
- Sesiones.
- Validaciones.
- Respuestas JSON.
- Redirecciones.

### Lógica de negocio

Son las reglas que dicen cómo debe funcionar la empresa:

- Solo usuarios activos pueden entrar.
- Tres intentos fallidos bloquean la cuenta.
- No se puede facturar sin resolución activa.
- Una venta descuenta inventario.
- La comisión se conserva en la factura.
- El vendedor solo ve sus ventas.
- Un NIT inválido puede producir rechazo simulado.

## Ejemplo: crear un cliente desde el POS

```text
FrontEnd: el usuario llena el modal.
   |
JavaScript: envía POST a FacturaController?action=crear_cliente_ajax.
   |
Controlador: valida identificación y nombre.
   |
Modelo Cliente: verifica duplicado y ejecuta INSERT.
   |
Controlador: devuelve JSON.
   |
JavaScript: selecciona el nuevo cliente en el carrito.
```

## Ejemplo: procesar una factura

```text
Vista Facturas.php
   |
public/js/facturas.js
   |
FacturaController::procesar()
   |
Factura::crearFactura()
   |
MySQL: facturas, detalle_factura, productos y resolucion_dian
   |
JSON de éxito
   |
SweetAlert y apertura de impresión
```

## Qué debe devolver cada capa

| Capa | Recibe | Devuelve |
|---|---|---|
| Vista | Datos del controlador/modelo | HTML o formulario |
| JavaScript | Eventos del usuario | AJAX o cambios visuales |
| Controlador | POST, GET, JSON y sesión | Redirección, HTML o JSON |
| Modelo | Datos ya validados | Arreglo, número, booleano o error |
| Base de datos | SQL preparado | Registros o resultado de operación |

## Regla de responsabilidad

- No mover una consulta SQL al JavaScript.
- No confiar en un total enviado por el navegador sin validarlo en el servidor.
- No poner toda la lógica de factura en la vista.
- No mostrar una venta sin filtrar el usuario cuando se trata de `mis_ventas.php`.
- No duplicar reglas de roles en cada pantalla si `header.php` ya controla el acceso.

## Cómo seguir un dato

Para investigar por qué un dato no aparece:

```text
Campo en la pantalla
  -> clave usada por la vista
  -> arreglo entregado por el controlador
  -> método llamado
  -> alias de la consulta SQL
  -> columna real de la tabla
```

Ejemplo de comisión:

```text
$usuario['porcentaje_comision']
  -> Usuario::obtenerTodos()
  -> u.porcentaje_comision
  -> usuarios.porcentaje_comision
```

## Diferencia entre dato mostrado y dato guardado

Que un formulario tenga un campo no significa que se guarde. Hay que comprobar la cadena completa:

```text
name del input -> $_POST -> parámetro del controlador -> parámetro del modelo -> INSERT/UPDATE -> SELECT -> vista
```
