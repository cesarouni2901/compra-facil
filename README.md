# CompraFácil

Catálogo de productos con búsqueda, filtros y ordenamiento, carrito de compras y formularios de registro. La comparación de productos se retiró del proyecto.

## Páginas

- `index.html`: página de inicio estática hecha con HTML y Bootstrap.
- `catalogo.php`: catálogo con búsqueda por texto, categoría, precio máximo y orden por precio o valoración.
- `carrito.php`: cantidades, subtotal y opciones de pago de demostración.
- `registro-cliente.php` y `registro-vendedor.php`: formularios con validación del lado del servidor.

## Tecnologías y organización

El proyecto utiliza PHP, HTML y CSS con Bootstrap 5.3.8 cargado como hoja de estilos. No requiere JavaScript. El código sigue una organización MVC sencilla:

- `app/models/`: producto, catálogo y carrito.
- `app/controllers/`: lógica de catálogo, carrito y registros.
- `app/views/`: páginas y fragmentos HTML/PHP.
- `app/bootstrap.php`: inicio de sesión, carga de clases y funciones de formato/escape.

Los comentarios del código describen la responsabilidad de las clases y los tipos de datos principales. El carrito se guarda en la sesión PHP. El catálogo y los precios son datos de ejemplo expresados en USD. Los registros se validan, pero no crean cuentas porque el proyecto todavía no tiene base de datos. El flujo de pago tampoco procesa transacciones.

## Inicio local

Inicia Apache desde XAMPP y abre `http://localhost/proyecto%20universudad/`. La página de inicio es `index.html`; el catálogo que procesa los filtros se ejecuta en `catalogo.php`. Bootstrap se carga desde el CDN oficial, así que la primera carga requiere conexión a internet.

## Imágenes

Guarda las imágenes autorizadas en `imagenes/productos/` y agrega la ruta relativa en el registro del producto dentro de `app/models/ProductCatalog.php`, por ejemplo `imagenes/productos/audifonos.jpg`.
