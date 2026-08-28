# 🛡️ Guía Anti-Sabotaje: Cómo sobrevivir a la evaluación

Esta guía está diseñada específicamente para que sepas **qué revisar cuando el instructor dañe intencionalmente el sistema**. En las evaluaciones, los instructores suelen atacar puntos clave (cuellos de botella) donde un solo cambio tumba todo el módulo. 

Aquí te explicamos **qué** suelen romper, **cómo** darte cuenta y **por qué** se soluciona de esa manera.

---

## 1. Sabotaje a la Conexión de Base de Datos (El clásico)

**El ataque:** El instructor cambia el usuario, la contraseña o el nombre de la base de datos.
**El síntoma:** Todo el sistema se cae. Sale una pantalla en blanco o un error fatal de PHP diciendo `mysqli_sql_exception: Access denied for user`.
**Cómo solucionarlo:**
1. Ve al archivo `config/database.php`.
2. Revisa las variables `$host`, `$user`, `$password` y `$bd`.
3. Asegúrate de que `$bd` diga exactamente el nombre de tu base de datos (ej. `"factuweb_pro"`).
**¿Por qué funciona así?:** Todo el sistema depende de la variable global `$conn` que se crea en este archivo. Si esto falla, los modelos no pueden hacer consultas.

---

## 2. Sabotaje en Rutas y Archivos (`require_once`)

**El ataque:** El instructor borra un `../` en un `require_once` o le cambia el nombre a un archivo importado.
**El síntoma:** Error fatal: `Failed opening required...` en la línea superior de algún controlador o vista.
**Cómo solucionarlo:**
1. Lee el error; te dirá exactamente el archivo y la línea (Ej: `dashboard.php on line 3`).
2. Ve a esa línea. Si dice `require_once('../../config/database.php');`, verifica que la cantidad de `../` sea correcta según dónde estés parado. 
**¿Por qué funciona así?:** En PHP nativo sin frameworks, los archivos se conectan "físicamente". Si la ruta está mal escrita, PHP detiene la ejecución inmediatamente por seguridad (`require` es estricto).

---

## 3. Sabotaje de Formularios (El "Botón que no hace nada")

**El ataque:** El instructor entra a una vista (ej. `views/admin/categorias.php`) y le cambia el atributo `name` a un `input` o cambia el `action` del formulario.
**El síntoma:** Llenas el formulario de Nueva Categoría, le das guardar y la página recarga pero no guarda nada, o guarda campos vacíos.
**Cómo solucionarlo:**
1. Abre el archivo de la vista (ej. `categorias.php`) y busca el `<form>`.
2. Verifica que el `action` apunte al controlador correcto (ej. `../../controllers/CategoriaController.php?action=guardar`).
3. Revisa los `name="..."` de los inputs. Si el input dice `name="nombre_categoria_x"`, el controlador intentará buscar `$_POST['nombre_categoria']` y fallará. Deben coincidir exactamente.
**¿Por qué funciona así?:** PHP recibe los datos de los formularios a través de la variable global `$_POST`. Las llaves de ese arreglo son estrictamente los atributos `name` del HTML.

---

## 4. Sabotaje al Sistema de Sesiones (Login infinito)

**El ataque:** Comentar la función `session_start()` o dañar la variable `$_SESSION['usuario']`.
**El síntoma:** Inicias sesión correctamente, pero te devuelve al login de inmediato. O sale un error `Undefined variable $_SESSION`.
**Cómo solucionarlo:**
1. Revisa `views/layouts/header.php`. Arriba del todo debe estar `session_start();`.
2. Revisa `controllers/auth/AuthController.php` en el método `login()`. Asegúrate de que los datos se guarden así: `$_SESSION['usuario'] = [...]`.
**¿Por qué funciona así?:** El protocolo HTTP no tiene memoria. PHP usa `session_start()` para crear una "memoria temporal" en el servidor y una cookie en tu navegador. Si no se inicia, en la siguiente página el servidor ya "olvidó" quién eres.

---

## 5. Sabotaje al Control de Roles (RBAC)

**El ataque:** Alterar la validación del rol. Un vendedor entra y ve el menú de administrador, o un administrador recibe "Acceso denegado".
**El síntoma:** El menú lateral carga las opciones incorrectas o hay redirecciones extrañas.
**Cómo solucionarlo:**
1. Ve a `views/layouts/header.php`.
2. Busca la lógica del menú: `if ($_SESSION['usuario']['id_rol'] == 1)` carga `sidebaradmin.php`, de lo contrario `sidebarseller.php`.
3. Revisa la lista blanca `$modulos_seller`. Si el instructor borró `'facturas.php'` de ahí, el vendedor no podrá facturar.
**¿Por qué funciona así?:** Toda la seguridad recae en el `header.php` porque **se carga en todas las páginas**. Él actúa como "el guardia de seguridad" de la discoteca antes de dejarte ver el contenido.

---

## 6. Sabotaje de AJAX y JavaScript (El POS se congela)

**El ataque:** El instructor cambia la URL de la petición `fetch` en el archivo JS o altera el formato JSON que devuelve el controlador.
**El síntoma:** Al agregar un producto en la factura, no pasa nada. Al darle F12 (Consola), ves errores en rojo.
**Cómo solucionarlo:**
1. Abre `public/js/facturas.js`. Busca la función `fetch(...)`. 
2. Verifica que la URL esté bien (ej. `../../controllers/FacturaController.php?action=...`).
3. Si la URL está bien, ve al controlador de facturas. Asegúrate de que devuelva JSON y **no imprima nada de HTML antes**. Debe decir `echo json_encode(...)` y terminar con `exit;`.
**¿Por qué funciona así?:** AJAX es comunicación silenciosa. JavaScript espera un objeto JSON perfecto. Si el controlador de PHP tiene un "echo 'hola';" accidental, el JSON se rompe y JavaScript lanza un error de "Unexpected token".

---

## 7. Sabotaje en Base de Datos (Sin tocar el código)

**El ataque:** El instructor entra a PhpMyAdmin o Laragon y hace algo como cambiar la resolución DIAN a "inactiva".
**El síntoma:** Al facturar sale error de "No hay resolución vigente". El código está perfecto, pero el sistema falla.
**Cómo solucionarlo:**
1. Entiende que **no todo error es de código**.
2. Piensa lógicamente: ¿Qué necesita una factura para crearse? Un cliente, productos con stock y una resolución activa.
3. Entra al módulo de Parametrización DIAN y verifica que la resolución esté activa.
4. Si un producto no se agrega, revisa si su `stock_actual` es mayor a 0 y si su estado es `activo`.

---

## 8. Sabotaje Visual (CSS/Bootstrap)

**El ataque:** Eliminar la vinculación a Bootstrap o dañar una clase fundamental en `index.php`.
**El síntoma:** El sistema se ve "feo", letras gigantes, botones sin estilo.
**Cómo solucionarlo:**
1. Revisa `<link href="../../public/bootstrap/css/bootstrap.min.css" rel="stylesheet">` en el `header.php` (y en `index.php`).
2. Verifica que la ruta relativa sea correcta respecto a la página que estás viendo.

---

## 🚀 Técnica para el examen (El Método de 3 pasos)

Si el instructor rompe algo y te dice "Arréglelo", **NO toques código a lo loco**. Sigue estos pasos frente a él (demuestra profesionalismo):

1. **Abre la Consola y la Red (F12):** En Google Chrome presiona F12.
   - Pestaña **Console**: Si hay letras rojas, te dirá si el instructor rompió el JavaScript.
   - Pestaña **Network (Red)**: Limpia, dale clic al botón que falla, y mira qué archivo PHP respondió y con qué error (404 no encontrado, 500 error del servidor).
2. **Sigue el hilo de Ariadna:**
   - ¿Qué botón toqué? (Miro el `action` o el evento en JS).
   - ¿A qué controlador fue? (Abro el controlador y veo el `action`).
   - ¿A qué modelo llamó? (Abro el modelo y reviso el query SQL).
3. **Usa `var_dump()` y `die()`:** Si no sabes qué está llegando a un controlador, pon `var_dump($_POST); die();` en la primera línea. Esto congelará la pantalla y te mostrará exactamente qué le mandó el formulario. Si llega vacío, el saboteo está en el HTML. Si llega lleno, el saboteo está en la consulta SQL.
