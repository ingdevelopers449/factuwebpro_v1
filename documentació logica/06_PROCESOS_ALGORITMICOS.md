# 6. Procesos algorítmicos

Un proceso algorítmico es una serie ordenada de pasos para resolver algo. En este proyecto los algoritmos son reglas de negocio, validaciones, ciclos y cálculos.

## Login y bloqueo

```text
1. Recibir correo y contraseña.
2. Validar que no estén vacíos.
3. Buscar usuario por correo.
4. Si está bloqueado, calcular si pasaron 15 minutos.
5. Comparar la contraseña con password_verify().
6. Si falla, aumentar intentos.
7. Al tercer fallo, bloquear.
8. Si funciona, regenerar sesión y redirigir según rol.
```

## Facturación completa

```text
1. El usuario arma el carrito.
2. JavaScript calcula subtotal e IVA.
3. El navegador envía JSON.
4. El controlador valida el JSON.
5. El modelo inicia una transacción.
6. Se busca resolución y consecutivo.
7. Se consulta comisión del usuario.
8. Se valida cliente.
9. Se inserta la cabecera de factura.
10. Por cada producto se inserta detalle y se descuenta stock.
11. Se actualiza el consecutivo.
12. Se confirma la transacción.
13. Se limpia el borrador.
```

Si un paso falla, se ejecuta `rollback()` para no dejar una factura incompleta.

## Ciclo anidado en una factura

Una venta contiene muchos detalles. Por eso existe un ciclo dentro del proceso de factura:

```php
foreach ($detalles as $detalle) {
    // consultar costo
    // insertar detalle
    // descontar stock
}
```

No se debe eliminar este ciclo sin reemplazarlo por una operación que procese todos los productos.

## Cálculo de totales

Para cada línea:

```text
subtotal de línea = cantidad x precio de venta
IVA = subtotal de línea x tarifa IVA / 100
Total = subtotal + IVA
```

El navegador muestra el cálculo rápidamente, pero el backend debe validar los datos importantes antes de guardar.

## Comisión

La comisión del usuario se consulta al procesar la venta y se copia a `facturas.porcentaje_comision_aplicado`. Así una comisión futura no cambia el historial de una factura antigua.

## Paginación

```text
1. Leer número de página.
2. Asegurar que la página sea mínimo 1.
3. Definir registros por página.
4. Calcular offset = (página - 1) x registros.
5. Consultar con LIMIT y OFFSET.
6. Consultar COUNT para saber páginas totales.
7. Dibujar Anterior y Siguiente.
```

## Transacciones

Una transacción agrupa varias operaciones:

```php
$this->conn->begin_transaction();
// INSERT y UPDATE relacionados
$this->conn->commit();
```

Ante una excepción:

```php
$this->conn->rollback();
```

La regla sencilla es: una factura debe guardarse completa o no guardarse.

## Algoritmo de diagnóstico

```text
1. Identificar la pantalla donde se ve el error.
2. Identificar el dato que falta o está incorrecto.
3. Revisar la vista.
4. Revisar el método del controlador.
5. Revisar el método del modelo.
6. Probar la consulta directamente con PHP.
7. Revisar la estructura SQL.
8. Corregir el punto más cercano al origen.
9. Ejecutar php -l.
10. Probar el flujo en el navegador.
```
