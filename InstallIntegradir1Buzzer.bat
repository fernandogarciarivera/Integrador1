@echo off
setlocal enabledelayedexpansion

cls
echo ========================================
echo  INSTALADOR / ACTUALIZADOR
echo  Laravel 12
echo ========================================
echo.
echo Selecciona una opcion:
echo   1 - Reinstalar TODO desde cero (borra proyecto y contenedores)
echo   2 - Solo actualizar configuraciones y migraciones (mantiene codigo)
echo.
set /p opcion="Opcion (1 o 2): "

if "%opcion%"=="1" goto reinstalar
if "%opcion%"=="2" goto actualizar
echo Opcion invalida.
pause
exit /b

:reinstalar
echo.
echo ⚠️  ADVERTENCIA: Se eliminaran TODOS los datos y se reinstalara desde cero.
set /p confirm="¿Estas seguro? (S/N): "
if /i not "%confirm%"=="S" (
    echo Operacion cancelada.
    pause
    exit /b
)

echo 🧹 Limpiando instalacion anterior...
docker compose down -v 2>nul
if exist src rmdir /s /q src
mkdir src

echo 📦 Creando proyecto Laravel 12...
docker run --rm -v "%cd%\src:/app" -w /app composer create-project laravel/laravel:^12 /app
if errorlevel 1 (
    echo ❌ Error al crear el proyecto
    pause
    exit /b
)
echo ✅ Proyecto creado.

echo.
echo ============================================================
echo 01 - Preparando estructura de storage...
echo ============================================================
if not exist src\storage mkdir src\storage
if not exist src\storage\framework mkdir src\storage\framework
if not exist src\storage\framework\sessions mkdir src\storage\framework\sessions
if not exist src\storage\framework\cache mkdir src\storage\framework\cache
if not exist src\storage\framework\cache\data mkdir src\storage\framework\cache\data
if not exist src\storage\framework\views mkdir src\storage\framework\views
if not exist src\storage\logs mkdir src\storage\logs
if not exist src\bootstrap\cache mkdir src\bootstrap\cache

goto configurar_env

:actualizar
echo.
echo 🔄 Modo actualizacion: se mantiene el codigo existente.
echo    Se ejecutaran: composer update y migraciones.
echo    Si los contenedores no estan corriendo, se levantaran.
echo.
set /p confirm="¿Continuar? (S/N): "
if /i not "%confirm%"=="S" (
    echo Operacion cancelada.
    pause
    exit /b
)

REM Asegurar que exista la carpeta src
if not exist src (
    echo ❌ No se encuentra la carpeta src. Ejecuta primero la opcion 1 (reinstalar).
    pause
    exit /b
)

goto configurar_env

:configurar_env
echo.
echo ============================================================
echo 02 - Generando .ENV Y APP_KEY (dentro del contenedor)
echo ============================================================
if exist src\.env (
    for /f "tokens=1,* delims==" %%A in ('findstr /b "APP_KEY=" src\.env') do set "APP_KEY=%%B"
)
if "%APP_KEY%"=="" for /f "delims=" %%K in ('docker run --rm -v "%cd%\src:/app" -w /app php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32));"') do set "APP_KEY=%%K"
if "%APP_KEY%"=="" (
    echo.
    echo No se pudo generar APP_KEY.
    pause
    exit /b 1
)
echo 📝 Creando/Actualizando archivo .env con valores correctos...
if not exist src\.env type nul > src\.env
docker compose up -d app
if errorlevel 1 goto :fallo
docker compose exec -T app bash -c "printf 'APP_NAME=UtpIntegradorBuzzer\nAPP_ENV=local\nAPP_DEBUG=true\nAPP_URL=http://localhost:8282\nAPP_TIMEZONE=UTC\n\nAPP_LOCALE=es\nAPP_FALLBACK_LOCALE=es\nAPP_FAKER_LOCALE=es_PE\n\nLOG_CHANNEL=stack\nLOG_LEVEL=debug\n\nDB_CONNECTION=mysql\nDB_HOST=mysql\nDB_PORT=3306\nDB_DATABASE=UtpIntegradorBuzzer\nDB_USERNAME=UtpIntegradorBuzzerBD\nDB_PASSWORD=12345678\n\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\nSESSION_ENCRYPT=false\nSESSION_PATH=/\nSESSION_DOMAIN=null\n\nBROADCAST_CONNECTION=log\nFILESYSTEM_DISK=local\nQUEUE_CONNECTION=database\n\nCACHE_STORE=redis\nCACHE_PREFIX=UtpIntegradortienda\n\nREDIS_CLIENT=phpredis\nREDIS_HOST=redis\nREDIS_PASSWORD=null\nREDIS_PORT=6379\n\nMAIL_MAILER=smtp\nMAIL_HOST=mailhog\nMAIL_PORT=1025\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_ENCRYPTION=null\nMAIL_FROM_ADDRESS=hello@example.com\nMAIL_FROM_NAME=UtpIntegradorBuzzer\n\nADMIN_JQADM=1\nAPP_KEY=%APP_KEY%\n\nXDEBUG_MODE=debug\n' > .env"
if errorlevel 1 goto :fallo
if not exist src\.env goto :fallo
findstr /b /c:"APP_NAME=UtpIntegradorBuzzer" /c:"APP_URL=http://localhost:8282" /c:"DB_CONNECTION=mysql" /c:"DB_HOST=mysql" src\.env >nul || goto :fallo
echo ✅ .env creado/actualizado con APP_KEY.

echo 🐳 Reconstruyendo la imagen 'app' (si el Dockerfile cambió) y levantando servicios...
REM echo Si has modificado el Dockerfile (p.ej. para habilitar WebP), se recomienda reconstruir la imagen del servicio 'app'.
REM docker compose build --no-cache app
docker compose up -d
if errorlevel 1 goto :fallo
docker compose up -d --force-recreate app
if errorlevel 1 goto :fallo
goto ejecutar_comandos

:ejecutar_comandos
echo ⏳ Esperando a que MySQL esté listo (healthcheck)...
set /a MYSQL_WAIT=0
:wait_mysql
docker inspect --format='{{.State.Health.Status}}' UtpIntegrador-mysql 2>nul | findstr "healthy" >nul
if errorlevel 1 (
    set /a MYSQL_WAIT+=1
    if !MYSQL_WAIT! GEQ 60 goto :fallo
    echo ⏳ Esperando MySQL...
    timeout /t 5 >nul
    goto wait_mysql
)
echo ✅ MySQL listo.

echo.
echo ============================================================
echo 04 - Publicando configuracion y assets de Laravel
echo ============================================================
REM docker compose exec -T app php artisan vendor:publish --tag=config --force
REM if errorlevel 1 goto :fallo
docker compose exec -T app php artisan vendor:publish --tag=public --force
if errorlevel 1 goto :fallo
docker compose exec -T app php artisan vendor:publish --tag=laravel-assets --force
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 05 - Instalando y configurando autenticacion (Laravel Breeze)
echo ============================================================
docker compose exec -T app composer require laravel/breeze:^^2.4 --with-all-dependencies --no-interaction
if errorlevel 1 goto :fallo
docker compose exec -T app php artisan breeze:install blade --no-interaction
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 06 - Configurando AppServiceProvider (Gate para admin)
echo ============================================================
docker compose exec -T app bash -c "printf '<?php\n\nnamespace App\Providers;\n\nuse Illuminate\Support\Facades\Gate;\nuse Illuminate\Support\ServiceProvider;\n\nclass AppServiceProvider extends ServiceProvider\n{\n    /**\n     * Register any application services.\n     */\n    public function register(): void\n    {\n        //\n    }\n\n    /**\n     * Bootstrap any application services.\n     */\n    public function boot(): void\n    {\n        Gate::define('\''admin'\'', function($user, $class, $roles) {\n            if( isset( $user->superuser ) && $user->superuser ) {\n                return true;\n            }\n            return app( '\''\\Aimeos\\Shop\\Base\\Support'\'' )->checkUserGroup( $user, $roles );\n        });\n    }\n}\n' > app/Providers/AppServiceProvider.php"

echo.
echo ============================================================
echo 07 - Verificando APP_KEY en el .env del contenedor
echo ============================================================
docker compose exec -T app grep APP_KEY .env

echo.
echo ============================================================
echo 08 - Limpiando cache de Laravel
echo ============================================================
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan cache:clear
docker compose exec -T app php artisan view:clear
docker compose exec -T app php artisan route:clear

echo.
echo ============================================================
echo 09 - Configurando permisos
echo ============================================================
docker compose exec -T app mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache 2>nul
docker compose exec -T app chmod -R 775 storage bootstrap/cache 2>nul
docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache public 2>nul
docker compose exec -T app mkdir -p /var/www/storage/framework/sessions
docker compose exec -T app chmod -R 775 /var/www/storage/framework 2>nul
docker compose exec -T app chown -R www-data:www-data /var/www/storage/framework 2>nul

echo.
echo ============================================================
echo 10 - Ejecutando migraciones (force)
echo ============================================================
docker compose exec -T app php artisan config:clear
if errorlevel 1 goto :fallo
docker compose exec -T app php artisan migrate --force
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 11 - Configurando la aplicacion
echo ============================================================
docker compose exec -T app php artisan migrate --force
if errorlevel 1 goto :fallo

REM echo.
REM echo ============================================================
REM echo 13 - Registrando rutas en web.php
REM if "%opcion%"=="2" goto rutas_preservadas
REM echo ============================================================
REM docker compose exec -T app bash -c "printf '<?php\n\nuse Illuminate\Support\Facades\Route;\nuse App\Http\Controllers\ProfileController;\n\nRoute::get('\''/'\'', function () {\n    return view('\''welcome'\'');\n});\n\nRoute::get('\''/dashboard'\'', function () {\n    return view('\''dashboard'\'');\n})->middleware(['\''auth'\''])->name('\''dashboard'\'');\n\nRoute::middleware('\''auth'\'')->group(function () {\n    Route::get('\''/profile'\'', [ProfileController::class, '\''edit'\''])->name('\''profile.edit'\'');\n    Route::patch('\''/profile'\'', [ProfileController::class, '\''update'\''])->name('\''profile.update'\'');\n    Route::delete('\''/profile'\'', [ProfileController::class, '\''destroy'\''])->name('\''profile.destroy'\'');\n});\n\nrequire __DIR__.'\''/auth.php'\'';\n\n// Aimeos routes\nRoute::group(['\''middleware'\'' => ['\''web'\'']], function() {\n    Route::get('\''/shop/{path?}'\'', '\''Aimeos\Shop\Controller\CatalogController@listAction'\'')\n        ->where('\''path'\'', '\''.*'\'')\n        ->name('\''aimeos_shop'\'');\n    Route::get('\''/admin/{path?}'\'', '\''Aimeos\Shop\Controller\AdminController@indexAction'\'')\n        ->where('\''path'\'', '\''.*'\'')\n        ->middleware(['\''auth'\'', '\''can:admin'\''])\n        ->name('\''aimeos_admin'\'');\n});' > routes/web.php"

:rutas_preservadas
if "%opcion%"=="2" echo Rutas existentes preservadas.

echo.
echo ============================================================
echo 12 - Instalando dependencias npm...
echo ============================================================
docker compose exec -T app npm install
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 13 - Recompilando assets (producción)...
echo ============================================================
docker compose exec -T app npm run build
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 14 - Forzando descubrimiento de paquetes y limpiando cache
echo ============================================================
docker compose exec -T app php artisan package:discover --ansi
if errorlevel 1 goto :fallo
docker compose exec -T app php artisan route:clear
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan view:clear

ECHO ===================================================================
ECHO  15: Super admin user exists (idempotent)
REM ===================================================================
docker exec UtpIntegrador-app php artisan tinker --execute="\App\Models\User::updateOrCreate(['email'=>'admin@integrador1.com'],['name'=>'Super Admin','password'=>bcrypt('123456'),'email_verified_at'=>now()]);"
if errorlevel 1 (
    echo  ERROR: Super admin creation failed
    set /a ERRORS+=1
    goto :fallo
)
echo  OK - Super admin ready

REM echo.
REM echo ============================================================
REM echo 18 - Datos demo (opcional)
REM echo ============================================================
REM set /p demo="¿Instalar datos demo? (S/N): "
REM if /i "%demo%"=="S" (
REM     echo 📦 Instalando datos demo...
REM     docker compose exec -T app php artisan db:seed
REM     if errorlevel 1 goto :fallo
REM )

echo.
echo ============================================================
echo 16 - Datos package.json 
echo ============================================================
if exist "%PROJECT_PATH%\package.json.template" (
    copy /y "%PROJECT_PATH%\package.json.template" "%PROJECT_PATH%\src\package.json" >nul
    echo  OK - package.json restored from template
)
if exist "%PROJECT_PATH%\package-lock.json.template" (
    copy /y "%PROJECT_PATH%\package-lock.json.template" "%PROJECT_PATH%\src\package-lock.json" >nul
    echo  OK - package-lock.json restored from template
)

echo.
echo ============================================================
echo 17 - Cacheando configuracion y reiniciando
echo ============================================================
CALL rebuild.bat
if errorlevel 1 goto :fallo

echo.
echo ============================================================
echo 18 - Cacheando configuracion y reiniciando
echo ============================================================
docker compose exec -T app php artisan config:cache
if errorlevel 1 goto :fallo
docker compose restart app
if errorlevel 1 goto :fallo

echo.
echo ========================================
echo ✅ PROCESO COMPLETADO!
echo ========================================
echo 🌐 Proyecto: http://localhost:8282
echo 🔐 Admin: http://localhost:8282
REM echo    Usuario: admin@UtpIntegrador.tienda
REM echo    Contraseña: %admin_pass%
echo 📊 phpMyAdmin: http://localhost:8081
echo 📧 MailHog: http://localhost:8025
echo.
echo ========================================
echo 📝 Comandos utiles:
echo   docker compose logs -f app     # Ver logs
echo   docker compose exec app bash   # Entrar al contenedor
echo.
goto :eof

:fallo
echo.
echo ❌ Instalacion detenida por un error. Revisa el comando anterior.
exit /b 1
