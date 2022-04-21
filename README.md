# CodeIgniter
Docker - CodeIgniter 3.1.13 (PHP 7.1) - FTP - SFTP

## Inicio
- Crear carpeta `upload` en la raíz de la aplicación, luego dentro de la carpeta `upload` crear las carpetas `temp`, `ci_ftp`, `ci_ftp_passwd` y `ci_sftp`
- En la carpeta `temp` se colocará los archivos temporales
- Las carpetas `ci_ftp`, `ci_ftp_passwd` y `ci_sftp` se utilizarán como volúmenes para el FTP y SFTP
- En la ruta `docker/php` se encuentra el archivo `init.sh` donde se asigna permisos a las carpetas `cache`, `temp`, `ci_ftp`, `ci_ftp_passwd` y `ci_sftp` cuando se levante el contendor
- Se agrega el archivo `.htaccess` donde se coloca regla para omitir el index.php de las url's
- Se modifica el archivo `application/config/config.php` para omitir el `index.php` de las url's en `['index_page']`, permitir cargar la carpeta `vendor` en `['composer_autoload']` e indicar como lenguaje el español en `['language']`
- Se agrega la ruta de temporales y las credenciales para conectar al ftp y sftp en `application/config/constants.php`

## Docker
- Para la primera vez que se levanta el proyecto con docker o se cambie los archivos de docker ejecutar:
```bash
sudo docker-compose up --build -d
```
- En las siguientes oportunidades ejecutar:

Para levantar:
```bash
sudo docker-compose start
```
Para detener:
```bash
sudo docker-compose stop
```
- Para ingresar al contenedor con php ejecutar:
```bash
sudo docker-compose exec webserver bash
```

- Instalar las dependencias con composer, dentro del contenedor con php ejecutar:
```bash
composer install
```
- Para ver el proyecto desde un navegador:

Sin virtualhost:
```bash
http://localhost:8282
```
Con virtualhost:

Si se usa Linux, agregar en /etc/hosts de la pc host la siguiente linea:
```bash
10.22.21.19    local.domain.com
```
- Para el FTP cuando se suban archivos, estos se colocarán dentro de la carpeta `ci_ftp`. Estructura del nombre del archivo `nombre_fechahora.txt` ejemplo: `miarchivo_20220418033314.txt`. Credenciales:
```bash
host: 10.22.21.23
port: 21
user: uadmin
pass: pasS123*
```
- Para probar funcionalidad en el FTP, ingresar al contenedor con php (webserver) y dentro ejecutar el siguiente comando:
```bash
php index.php cron/work_ftp_sftp work_in_ftp
```
- Para el SFTP se colocarán los archivos dentro de la carpeta `ci_sftp`. Estructura del nombre del archivo `nombre_fechahora.txt` ejemplo: `miarchivo_20220418033314.txt`. Credenciales:
```bash
host: 10.22.21.24
port: 22
user: admin
pass: pasS123*
```
- Para probar funcionalidad en el SFTP, ingresar al contenedor con php (webserver) y dentro ejecutar el siguiente comando:
```bash
php index.php cron/work_ftp_sftp work_in_ftp
```