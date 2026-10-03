# CompraFácil

Catálogo de productos con búsqueda, filtros, opciones de producto, carrito, cuentas de cliente/vendedor y publicación de productos con varias fotografías.

## Páginas

- `index.php`: inicio que conserva y muestra el estado de la sesión.
- `catalogo.php`: catálogo con búsqueda por texto, categoría, marca, precio y ordenamiento.
- `producto.php`: detalle, distintas vistas fotográficas y opciones de color/capacidad.
- `producto-nuevo.php`: formulario de publicación disponible para vendedores adultos con sesión iniciada; admite seis fotos por producto.
- `carrito.php`: cantidades, variantes, subtotal y pago de demostración. Solo cuentas adultas autenticadas pueden confirmar un pedido.
- `registro-cliente.php`, `registro-vendedor.php` y `login.php`: registro y acceso a cuentas.

## Código y tecnologías

El proyecto utiliza PHP, HTML y CSS con Bootstrap 5 como framework visual, sin base de datos. JavaScript se limita a filtrar caracteres, mostrar/ocultar claves y comparar las claves en los formularios de cuenta. Mantiene una estructura MVC introductoria en `app/models/`, `app/controllers/` y `app/views/`. Las entradas se validan en el servidor además de las restricciones HTML.

## Privacidad y credenciales

Las cuentas se guardan en `app/private/users.json`, una carpeta bloqueada para acceso web directo por `app/.htaccess` e ignorada por Git. El servidor debe poder escribir allí. Los datos identificables (nombre, apellido, correo, documento, nacimiento, teléfono y negocio) se protegen con **AES-256-GCM**, que cifra y detecta modificaciones. Se usa **HMAC-SHA-256** para comparar correo/documento sin guardarlos en claro. Las claves de acceso se procesan con `password_hash(PASSWORD_DEFAULT)` y se verifican con `password_verify()`; no se pueden descifrar.

La clave de cifrado se genera automáticamente en `app/config.local.php`; ese archivo también está excluido de Git y protegido por `.htaccess`. No borres ni compartas ese archivo si ya existen cuentas: sin la clave no se podrán recuperar los datos cifrados. Conserva una copia protegida de la clave y del archivo de usuarios.

Las cuentas y productos nuevos se guardan en archivos JSON dentro de `app/private/`, que se bloquea para acceso web directo y se excluye de Git. El servidor PHP debe tener permiso para escribir allí. Esta persistencia de archivo es para la demostración académica; no tiene las transacciones, respaldos ni control concurrente de un sistema de producción. Usa HTTPS al publicar el sitio y no subas `app/private/users.json`, `app/private/products.json`, `app/config.local.php` ni fotos de usuarios al repositorio. La carpeta de fotos públicas contiene solamente imágenes de productos.

## Edad y validaciones

Clientes y vendedores deben tener al menos 18 años. El servidor valida la fecha de nacimiento, limita claves a 8–12 caracteres con una mayúscula y un símbolo, acepta solo letras/espacios en nombre y apellido, y solo dígitos en el documento.

## Inicio local

Inicia Apache en XAMPP y abre `http://localhost/proyecto%20universudad/`. Bootstrap se carga desde el CDN oficial, por lo que la primera carga requiere conexión a internet. El pago continúa siendo de demostración y no procesa transacciones.
