# Aplicación de Bibliotecas

Aplicación web desarrollada en PHP sobre **CodeIgniter 4** para la gestión de bibliotecas: catálogo de libros, autores, ejemplares, préstamos y devoluciones, y gestión de usuarios/socios, capacidad para gestionar varias bibliotecas, gestión de socios, reservas, etc.)

## Requisitos

- PHP 8.1 o superior (con extensiones `intl`, `mbstring` y `mysqli` activas)
- MySQL / MariaDB
- Apache con `mod_rewrite` activo (WAMP en local, Linux en producción)
- Composer (solo si se actualizan dependencias; el código subido ya incluye `vendor/`)

## Instalación

### 1. Creación de la base de datos

Crear la base de datos vacía en MySQL (desde phpMyAdmin o por consola):

```sql
CREATE DATABASE biblio2 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Si el proyecto incluye un volcado SQL inicial, importarlo ahora.

### 2. Copia de archivos

- **Local (WAMP)**: copiar la carpeta del proyecto a `C:\wamp64\www\bibliotecas`.
- **Producción (Linux)**: subir los archivos al directorio correspondiente del hosting, con `public/` como document root del virtualhost. Asegurarse de que `writable/` tiene permisos de escritura para el usuario del servidor web.

### 3. Configuración

#### `app/Config/App.php`

Ajustar la URL base de la aplicación:

```php
public string $baseURL = 'http://bibliotecas.test/';
```

> En producción, la URL real del dominio/subdominio, con la barra final.

#### `app/Config/Database.php`

Configurar la conexión a la base de datos:

```php
public array $default = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'bibliotecas',
    'DBDriver' => 'MySQLi',
    // ...
];
```

#### `.env`

Copiar el archivo `env` que trae CodeIgniter a `.env` y descomentar/ajustar:

```
CI_ENVIRONMENT = development   ; en producción: production

app.baseURL = 'http://bibliotecas.test/'

database.default.hostname = localhost
database.default.database = bibliotecas
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

> Lo definido en `.env` tiene prioridad sobre `Config/App.php` y `Config/Database.php`, así que basta con configurarlo aquí. Los archivos de `Config/` se dejan como valores por defecto.

#### `.htaccess`

El `public/.htaccess` que trae CodeIgniter ya redirige todo al `index.php`. Solo hay que tocarlo si la aplicación **no** cuelga de la raíz del dominio, añadiendo el subdirectorio:

```apache
RewriteBase /bibliotecas/
```

Si en producción va en subdominio propio (raíz), esta línea no hace falta; si va en carpeta, sí.

### 4. Migraciones y primer arranque

Si el proyecto usa migraciones:

```cmd
php spark migrate
```

En principio no haría falta generar migraciones, con importar la BD que está en el directorio BD (biblio2.sql) es suficiente.

Comprobar el acceso en el navegador (`http://bibliotecas.test/` en local, o la URL de producción). En local también se puede levantar con el servidor integrado:

```cmd
php spark serve
```

## Notas

- En producción, recordar que Linux distingue mayúsculas/minúsculas en nombres de archivo: un controlador o vista con el case incorrecto funciona en Windows y falla en el servidor.
- Para pasar a producción: `CI_ENVIRONMENT = production` en el `.env` del servidor, nunca subir el `.env` local con credenciales de WAMP.
