# Integrador1

Aplicación Laravel para desarrollo local con Docker Compose.

## Requisitos

- Git
- Docker con Docker Compose v2
- Acceso al repositorio y, para una base inicial, al dump de datos del proyecto

## Primera ejecución en Bash

Cada colaborador que desarrolle localmente necesita su propio `.env` y sus propios contenedores. Desde una terminal Bash:

```bash
git clone https://github.com/fernandogarciarivera/Integrador1.git
cd Integrador1
cp src/.env.example src/.env
```

Edita `src/.env` y configura la conexión a MySQL de Docker (no uses los valores SQLite del ejemplo):

```dotenv
APP_NAME=Integrador1
APP_URL=http://localhost:8282
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=UtpIntegradorBuzzer
DB_USERNAME=usuario_local
DB_PASSWORD=contraseña_local
REDIS_HOST=redis
```

Reemplaza `usuario_local` y `contraseña_local` con las credenciales de desarrollo configuradas para MySQL en `docker-compose.yml` y `docker/mysql/init.sql`. No compartas ni subas `src/.env`.

Valida la configuración y levanta los servicios, incluyendo Vite para recarga en caliente:

```bash
docker compose config -q
docker compose --profile dev up -d --build
docker compose ps
```

En una instalación nueva, la base del contenedor está vacía. Importa el dump de desarrollo **solo en una base local nueva**: este archivo elimina y recrea tablas existentes.

```bash
docker compose exec -T mysql sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" UtpIntegradorBuzzer' \
  < db/schema/dump-UtpIntegradorBuzzer-202610230951.sql
```

Genera la clave de Laravel, aplica migraciones pendientes y crea el enlace de archivos públicos:

```bash
docker compose exec -T app php artisan key:generate
docker compose exec -T -u www-data app php artisan migrate --force
docker compose exec -T -u www-data app php artisan storage:link
```

Abre <http://localhost:8282>. El contenedor `app` instala dependencias y compila assets durante el primer arranque.

## Colaborar y actualizar

Cada persona trabaja en su rama y base local. Para actualizar una copia existente:

```bash
git pull --ff-only
docker compose --profile dev up -d --build
docker compose exec -T -u www-data app php artisan migrate --force
```

Para un cambio nuevo, crea una rama y publícala para revisión:

```bash
git switch -c feature/nombre-del-cambio
git add ruta/al/archivo
git commit -m "Describe el cambio"
git push -u origin feature/nombre-del-cambio
```

Para compartir una instancia de desarrollo en la red local, levántala en una sola computadora, configura `APP_URL=http://IP_DEL_HOST:8282` en `src/.env` y permite el puerto `8282` en su firewall. Los demás colaboradores abren `http://IP_DEL_HOST:8282`; no importes el dump nuevamente en una base compartida.

```bash
LAN_HOST_IP=IP_DEL_HOST docker compose --profile dev up -d --build
```

## Comandos frecuentes

```bash
# Suite de pruebas Laravel
docker compose exec -T -u www-data app php artisan test

# Ver logs
docker compose logs -f --tail=100 app nginx vite

# Detener servicios sin borrar la base ni archivos persistentes
docker compose down

# Volver a iniciar
docker compose --profile dev up -d
```

No uses `docker compose down -v` salvo que quieras eliminar los volúmenes locales, incluida la base de datos.

Para los pasos de instalación originales y comandos operativos adicionales, consulta [leeme.txt](./leeme.txt). La matriz de validación está en [QA-CASOS-DE-PRUEBA.md](./QA-CASOS-DE-PRUEBA.md).
