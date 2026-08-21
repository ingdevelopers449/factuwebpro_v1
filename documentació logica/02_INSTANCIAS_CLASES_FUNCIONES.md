# 2. Instancias, clases y funciones

## Clase

Una clase es un molde que agrupa datos y acciones. En este proyecto, `Usuario` agrupa todo lo relacionado con usuarios.

```php
class Usuario
{
    public function obtenerTodos()
    {
        // Consulta usuarios y devuelve un arreglo.
    }
}
```

## Instancia

Una instancia es un objeto creado a partir de una clase. Se crea con `new`:

```php
$facturaModel = new Factura();
$facturas = $facturaModel->obtenerVentasPorUsuario($id_usuario);
```

La variable `$facturaModel` es una instancia de `Factura`.

## Constructor

`__construct()` se ejecuta automáticamente al crear la instancia. En los modelos conecta la clase con `$conn`.

```php
public function __construct()
{
    global $conn;
    $this->conn = $conn;
}
```

## Propiedades

Una propiedad guarda un dato dentro de la clase:

```php
private \mysqli $conn;
```

`private` significa que solo la clase puede usar directamente esa propiedad.

## Métodos y funciones

Dentro de una clase, una función se llama método:

```php
public function eliminar(int $id_cliente)
{
    // Elimina un cliente.
}
```

Una función recibe entradas y puede devolver una salida:

```php
public function obtenerPorId(int $id_cliente): ?array
{
    // Devuelve un arreglo o null.
}
```

- `int`: espera un entero.
- `string`: espera texto.
- `float`: espera decimal.
- `array`: espera o devuelve una lista.
- `?array`: puede devolver una lista o `null`.
- `bool`: devuelve `true` o `false`.

## Cómo elegir dónde poner una función

- Si consulta o modifica MySQL: modelo.
- Si valida una petición o decide un flujo: controlador.
- Si imprime datos o crea formularios: vista.
- Si cambia el carrito en el navegador: JavaScript.

## Ejemplo de flujo

```php
// Vista o formulario
$clienteModel = new Cliente();

// Llamada al método
$cliente = $clienteModel->obtenerPorId($id_cliente);

// Uso del resultado
if ($cliente) {
    echo htmlspecialchars($cliente['nombre_razon_social']);
}
```

## Errores de instancias

Si aparece `Class not found`:

1. Revisar `require_once`.
2. Revisar que el nombre de la clase coincida con el archivo.
3. Revisar mayúsculas y minúsculas.
4. Revisar que la instancia use `new NombreClase()`.

Si aparece `Call to undefined method`:

1. Buscar si el método existe.
2. Revisar el nombre exacto.
3. Revisar los parámetros enviados.
4. Confirmar que se está creando la clase correcta.
