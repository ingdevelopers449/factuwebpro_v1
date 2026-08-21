# Documentación lógica de FactuWeb PRO

## Propósito

Esta carpeta explica cómo funciona el proyecto con palabras sencillas. Está pensada para:

- Un desarrollador junior que necesita entender el código.
- Una IA que debe localizar y corregir errores.
- Una persona que necesita saber qué archivo tocar sin romper otras partes.

## Orden recomendado de lectura

1. [Modelos](01_MODELOS.md)
2. [Instancias, clases y funciones](02_INSTANCIAS_CLASES_FUNCIONES.md)
3. [Controladores](03_CONTROLADORES.md)
4. [Vistas y FrontEnd](04_VISTAS_FRONTEND.md)
5. [Foreach, for y condiciones](05_FOREACH_FOR_CONDICIONES.md)
6. [Procesos algorítmicos](06_PROCESOS_ALGORITMICOS.md)
7. [Módulos](07_MODULOS.md)
8. [Relación FrontEnd, BackEnd y negocio](08_RELACION_FRONTEND_BACKEND_NEGOCIO.md)
9. [Guía para diagnosticar errores](09_GUIA_DIAGNOSTICO_IA.md)

## Arquitectura resumida

FactuWeb PRO usa MVC en PHP sin framework:

```text
Usuario
  |
  v
Vista PHP + JavaScript
  |
  v
Controlador
  |
  v
Modelo
  |
  v
MySQL
```

- **Vista:** muestra pantallas y recibe datos de formularios.
- **Controlador:** decide qué proceso ejecutar y valida la entrada.
- **Modelo:** consulta o modifica la base de datos.
- **Base de datos:** guarda usuarios, productos, clientes, facturas y configuración.

## Archivos importantes

| Área | Ubicación | Responsabilidad |
|---|---|---|
| Configuración | `config/database.php` | Conexión MySQL y zona horaria |
| Controladores | `controllers/` | Procesos de cada módulo |
| Modelos | `models/` | Acceso a datos y reglas del negocio |
| Vistas | `views/` | HTML, PHP de presentación y formularios |
| JavaScript | `public/js/` | Interacción del POS y AJAX |
| CSS | `public/css/style.css` | Diseño visual |
| Base de datos | `sql/DB-FACTUWEBPRO.sql` | Tablas, campos y relaciones |
| Reglas para IA | `.agents/AGENTS.md` | Convenciones obligatorias |

## Reglas que nunca se deben olvidar

- Usar `prepare()` y `bind_param()` en consultas que reciban datos.
- Las vistas protegidas deben cargar `views/layouts/header.php`.
- El rol 1 es administrador y el rol 2 es vendedor.
- El vendedor solo debe ver sus propias ventas.
- Las alertas de operación usan `$_SESSION['alert']` y SweetAlert2.
- La zona horaria es `America/Bogota`.
- No modificar el footer salvo que el cambio sea para el Centro de Ayuda.
