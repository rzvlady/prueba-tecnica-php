# Prueba Técnica - Sistema de Gestión de Clientes

Este proyecto es una aplicación web desarrollada en PHP nativo orientada a la gestión de clientes, integrando un sistema de autenticación, operaciones CRUD y registro en bitácora de transacciones. 

## Cómo ejecutar el proyecto

Para correr este proyecto en tu entorno local, sigue estos pasos:

### 1. Requisitos previos
- PHP 8.2
- Servidor de base de datos MySQL / MariaDB (por ejemplo, a través de XAMPP, Laragon, o MySQL nativo).
- Git (opcional, para clonar el repositorio).

### 2. Configuración de la base de datos
1. Crea una nueva base de datos en MySQL llamada `prueba_tecnica`.
2. Importa el esquema de tablas proporcionado (usuarios, clientes, bitácora) dentro de la base de datos recién creada (referenciado en los requerimientos o provisto aparte).
3. Para poder iniciar sesión, es necesario insertar un usuario en la base de datos con una contraseña encriptada. Puedes generar el hash ejecutando localmente en tu terminal el archivo `hash.php`:
   ```bash
   php hash.php
   ```
   Copia el hash generado por el script y colócalo al momento de ejecutar tu script de MySQL (el `INSERT` de la tabla usuarios) para crear el usuario correctamente.
4. (Opcional) Verifica las credenciales de conexión en el archivo `config/database.php`. Por defecto está configurado así:
   - Host: `localhost`
   - Base de datos: `prueba_tecnica`
   - Usuario: `root`
   - Contraseña: `""` (vacío)

### 3. Levantar el servidor
Puedes utilizar el servidor interno de PHP para ejecutar el proyecto sin necesidad de mover los archivos a directorios específicos como `htdocs` o `www`.

Abre tu terminal en la raíz del proyecto y ejecuta el siguiente comando:
```bash
php -S localhost:8000
```

### 4. Acceder al sistema
Abre tu navegador web y dirígete a:
```
http://localhost:8000
```
Dependiendo del estado de tu sesión, el sistema te mostrará automáticamente la pantalla de Login o te redirigirá al Dashboard.

---

## Estructura de carpetas

El proyecto está organizado siguiendo el patrón de arquitectura **MVC (Modelo - Vista - Controlador)** para mantener el código ordenado, modular y escalable.

```text
prueba_tecnica/
├── assets/             # Recursos estáticos
│   ├── css/            # Hojas de estilo personalizadas
│   └── js/             # Archivos JavaScript (ej. lógica AJAX en cliente.js)
├── config/             # Configuración global del sistema
│   └── database.php    # Clase de conexión a la base de datos usando PDO
├── controllers/        # Controladores (Capa de lógica de negocio)
│   ├── AuthController.php    # Lógica de login/logout
│   ├── ClienteController.php # Lógica del CRUD de clientes y peticiones AJAX
│   └── logout.php            # Script independiente para el cierre de sesión
├── models/             # Modelos (Capa de abstracción de datos)
│   ├── Bitacora.php    # Consultas e inserciones para la bitácora
│   ├── Cliente.php     # Consultas a la base de datos de la entidad Cliente
│   └── Usuario.php     # Validaciones y consultas de los usuarios del sistema
├── views/              # Vistas (Capa de presentación o Interfaz de Usuario)
│   ├── dashboard.php       # Panel principal / Listado general de clientes
│   ├── login.php           # Pantalla de inicio de sesión
│   └── transacciones.php   # Pantalla de visualización de bitácoras
├── index.php           # Punto de entrada principal (Redirecciona según la sesión)
```

---

## Decisiones técnicas tomadas

Durante el desarrollo de la prueba técnica, se tomaron consideraciones enfocadas en la seguridad, rendimiento y buenas prácticas de desarrollo:

### 1. Patrón de Arquitectura MVC (Modelo-Vista-Controlador)
Se estructuró el proyecto en MVC sin depender de un framework grande (como Laravel o Symfony) para demostrar el dominio nativo del lenguaje PHP y la capacidad de estructurar aplicaciones escalables. Esto permite una clara separación de responsabilidades: los modelos se encargan de los datos, los controladores de la lógica, y las vistas de la interfaz.

### 2. Uso de PDO (PHP Data Objects) y Consultas Preparadas
Para interactuar con MySQL, la conexión (`config/database.php`) y los Modelos (`models/`) utilizan la librería PDO. Todas las consultas implementan **Sentencias Preparadas (Prepared Statements)** (`bindParam`) en lugar de concatenar cadenas. Esta es una decisión crucial y crítica de seguridad para proteger el sistema íntegramente de ataques de **Inyección SQL**.

### 3. Autenticación y Manejo de Sesiones
Se implementó el uso de variables superglobales de sesión (`$_SESSION`) nativas de PHP para proteger las diferentes rutas y funcionalidades (`index.php`, `views/dashboard.php`). Si el usuario no ha iniciado sesión, siempre será redirigido al acceso denegado / pantalla de Login, protegiendo las vistas y peticiones al backend.

### 4. Peticiones Asíncronas (AJAX / Fetch UI)
El CRUD en la vista del dashboard no provoca recargas de la página completa, logrando una experiencia de usuario (UX) bastante fluida. Esto se logró enviando la información con AJAX a través de JavaScript vanilla (`assets/js/cliente.js`) que se comunica con una API rudimentaria estructurada en `ClienteController.php` utilizando estándar de respuestas estilo JSON (`json_encode()`).

### 5. Framework CSS (Bootstrap 5) y UI Moderna
Para el maquetado y estilos, se incorporó **Bootstrap 5 vía CDN** y elementos vectoriales. Esto permite acelerar de manera considerable la implementación de vistas responsivas (Mobile First), tablas estilizadas, grillas y componentes modales dinámicos (`views/dashboard.php`) sin sobrecargar y ensuciar el repositorio con hojas de estilos (`CSS`) innecesarias.

### 6. Eliminación Lógica (Soft Delete)
Tal como es reflejado en la capa del Modelo, en lugar de realizar una instrucción `DELETE` definitiva en la base de datos, se optó por un Update para cambiar el estado activo del registro. Esta decisión técnica resguarda la integridad e historial de los datos, previniendo referencias rotas en el sistema.
