# 5. Foreach, for y condiciones

## `foreach`

Se usa para recorrer una lista. Es la forma más común de imprimir registros de la base de datos:

```php
foreach ($usuarios as $usuario) {
    echo htmlspecialchars($usuario['nombre']);
}
```

En el proyecto aparece para:

- Usuarios en `gempleados.php`.
- Ventas en `mis_ventas.php`.
- Facturas en el dashboard.
- Productos del carrito.
- Detalles de una factura.
- Opciones de roles y estados.

## `foreach` con clave y valor

```php
foreach ($estados as $valor => $texto) {
    echo $valor;
    echo $texto;
}
```

Se usa cuando una lista tiene un código y una etiqueta visible.

## `for`

Se usa cuando conocemos el número de vueltas o necesitamos contar hacia atrás:

```php
for ($i = 6; $i >= 0; $i--) {
    // Rellena los últimos siete días del gráfico.
}
```

## `if`, `elseif` y `else`

Permiten elegir qué mostrar o qué proceso ejecutar:

```php
if ($comision > 0) {
    echo $comision . '%';
} else {
    echo '—';
}
```

Ejemplos del sistema:

- Estado aceptado, rechazado o pendiente de una factura.
- Rol administrador o vendedor.
- Usuario activo, bloqueado o inactivo.
- Carrito vacío o con productos.
- Producto con stock o agotado.

## Condiciones anidadas

Una condición puede estar dentro de otra:

```php
if ($id_cliente) {
    if (strlen($identificacion) < 5) {
        $estado_dian = 'rechazada';
    }
}
```

Primero se revisa si existe cliente. Después se valida su identificación.

## `empty`, `isset` y operador `??`

- `isset($dato)`: revisa si la variable existe y no es `null`.
- `empty($dato)`: revisa si está vacío.
- `$dato ?? 'valor'`: usa un valor alternativo si no existe.

```php
$nombre = $_POST['nombre'] ?? '';
```

## Errores frecuentes al recorrer listas

- Usar una clave que no existe, por ejemplo `nombre` cuando la consulta devuelve `nombre_producto`.
- No comprobar si el arreglo está vacío.
- Imprimir valores sin `htmlspecialchars()`.
- Confundir el índice del arreglo con el ID de la base de datos.
- Hacer una consulta dentro de un `foreach` sin necesidad y volver lenta la pantalla.
