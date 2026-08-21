# 9. Guía de diagnóstico para la IA

## Objetivo

Cuando aparezca un error, la IA debe leer primero esta guía y después revisar el archivo más cercano al problema. No debe cambiar archivos al azar.

## Paso 1: clasificar el error

| Síntoma | Primer lugar para revisar |
|---|---|
| Pantalla en blanco | PHP, `require_once`, error fatal |
| `Class not found` | Nombre de clase y archivo incluido |
| `Call to undefined method` | Método y nombre de la instancia |
| Tabla vacía | Consulta, filtros, conexión y claves del arreglo |
| Dato no aparece | SELECT, alias, controlador y vista |
| Formulario no guarda | `name`, `$_POST`, controlador e INSERT/UPDATE |
| AJAX falla | JSON, Network, headers y warnings PHP |
| Usuario entra a módulo incorrecto | `header.php` y `id_rol` |
| Factura no se crea | Resolución, stock, transacción y error SQL |
| Paginación incorrecta | COUNT, LIMIT, OFFSET y parámetros |

## Paso 2: localizar el flujo

Usar esta secuencia:

```text
Vista -> Controlador -> Modelo -> Base de datos
```

En un proceso AJAX:

```text
Vista -> JavaScript -> Controlador JSON -> Modelo -> Base de datos
```

## Paso 3: revisar datos reales

No asumir que el dato existe. Revisar:

1. Nombre de la tabla.
2. Nombre de la columna.
3. Tipo de dato.
4. Valor en MySQL.
5. Alias usado en el `SELECT`.
6. Clave usada en PHP.

## Paso 4: validar PHP

Desde la raíz del proyecto:

```powershell
php -l models/Factura.php
php -l controllers/FacturaController.php
php -l views/admin/dashboard.php
```

`php -l` revisa sintaxis sin ejecutar el proceso.

## Paso 5: validar consultas

Siempre que haya entrada variable:

```php
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
```

Si no hay resultados, revisar los parámetros antes de cambiar la consulta.

## Errores de sesión

Revisar:

- `session_start()` antes de usar `$_SESSION`.
- Que el login llene `$_SESSION['usuario']`.
- Que exista `id_usuario`, `id_empresa` e `id_rol`.
- Que no se destruya la sesión por timeout.
- Que el rol tenga acceso en `header.php`.

## Errores de facturación

Comprobar en este orden:

1. Existe usuario autenticado.
2. Existe empresa en sesión.
3. Hay resolución DIAN activa.
4. El consecutivo está dentro del rango.
5. El carrito tiene detalles.
6. Los productos existen y tienen stock.
7. Las columnas de `facturas` coinciden con el INSERT.
8. La transacción termina en `commit()`.
9. El borrador se limpia después del éxito.

## Errores de vista

Si el dato existe pero no aparece:

- Confirmar la clave, por ejemplo `porcentaje_comision`.
- Usar `var_export($arreglo)` temporalmente en desarrollo.
- Revisar si el dato llega como `null`, texto o número.
- Revisar el `foreach` correcto.
- Escapar texto con `htmlspecialchars()`.

## Errores de AJAX

Si el navegador dice que no puede leer JSON:

1. Abrir la pestaña Network.
2. Revisar la respuesta completa.
3. Buscar warnings, notices o HTML antes del JSON.
4. Revisar que el controlador use `header('Content-Type: application/json')`.
5. Confirmar que todos los caminos terminen con `exit`.

## Paginación

Para una página `n`:

```text
offset = (n - 1) x registros_por_pagina
```

Comprobar:

- Página mínima 1.
- `LIMIT` y `OFFSET` como enteros.
- `COUNT(*)` con los mismos filtros.
- Enlaces que conserven fechas y búsquedas.
- Totales calculados sobre todo el periodo, no solo la página actual.

## Reglas de reparación

- Corregir la causa original, no esconder el error.
- Hacer el cambio más pequeño posible.
- No modificar archivos que no participan en el flujo.
- No quitar validaciones de rol.
- No reemplazar consultas preparadas por concatenación.
- Ejecutar `php -l` después de cada cambio.
- Probar el flujo real en el navegador después de validar sintaxis.
- No borrar cambios existentes del usuario.

## Información que debe dejar una IA al terminar

1. Qué archivo cambió.
2. Qué causa encontró.
3. Qué comportamiento corrigió.
4. Qué comando o prueba ejecutó.
5. Qué riesgo o pendiente permanece.
