@echo off
REM ===================================================================
REM PEKETIENDA PROJECT REBUILD - Comprehensive Setup Script
REM ===================================================================
REM This script validates and rebuilds the entire project environment

setlocal enabledelayedexpansion

REM --- Detectar IP LAN ---
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    set "LAN_IP=%%a"
    set "LAN_IP=!LAN_IP:~1!"
    goto :got_ip
)
:got_ip

if "%LAN_IP%"=="" (
    echo No se pudo detectar la IP LAN. Abortando.
    exit /b 1
)

REM --- Puerto LAN (por defecto 8282 si no se pasa argumento) ---
set "LAN_PORT=%~1"
if "%LAN_PORT%"=="" set "LAN_PORT=8282"

REM --- Asegurar que src/.env existe ---
if not exist "src\.env" (
    echo No existe src\.env. Abortando.
    exit /b 1
)

echo LAN_HOST_IP=%LAN_IP%
echo LAN_PORT=%LAN_PORT%

set "PROJECT_PATH=%~dp0"
if "%PROJECT_PATH:~-1%"=="\" set "PROJECT_PATH=%PROJECT_PATH:~0,-1%"
pushd "%PROJECT_PATH%" >nul
set "ERRORS=0"

echo.
echo ============================================================
echo  Project Validation and Rebuild
echo ============================================================
echo.

ECHO ===================================================================
ECHO STEP 1: Validar Docker Installacion
ECHO ===================================================================
echo [1/7] Validando Docker installacion...
docker --version >nul 2>&1
if errorlevel 1 (
    echo  ERROR: Docker is not installed or not in PATH
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - Docker found
echo.

ECHO ===================================================================
ECHO STEP 2: Validar Project Structure
ECHO ===================================================================
echo [2/7] Validando project structure...
set "MISSING=0"

if not exist "%PROJECT_PATH%\src" (
    echo  ERROR: src directory missing
    set /a MISSING+=1
)
if not exist "%PROJECT_PATH%\docker" (
    echo  ERROR: docker directory missing
    set /a MISSING+=1
)
if not exist "%PROJECT_PATH%\docker-compose.yml" (
    echo  ERROR: docker-compose.yml missing
    set /a MISSING+=1
)
if not exist "%PROJECT_PATH%\src\package.json" (
    echo  ERROR: package.json missing
    set /a MISSING+=1
)

if !MISSING! equ 0 (
    echo  OK - All required directories found
) else (
    echo  ERROR: Missing !MISSING! critical directories
    set /a ERRORS+=!MISSING!
)
echo.

ECHO ===================================================================
ECHO STEP 3A: Validate and refresh frontend dependencies from the lockfile
ECHO ===================================================================
if exist "%PROJECT_PATH%\package.json.template" (
    copy /y "%PROJECT_PATH%\package.json.template" "%PROJECT_PATH%\src\package.json" >nul
    echo  OK - package.json restored from template
)
if exist "%PROJECT_PATH%\package-lock.json.template" (
    copy /y "%PROJECT_PATH%\package-lock.json.template" "%PROJECT_PATH%\src\package-lock.json" >nul
    echo  OK - package-lock.json restored from template
)
if exist "%PROJECT_PATH%\src\package.json" (
    echo [3A/7] Refreshing npm dependencies from package-lock.json...
    pushd "%PROJECT_PATH%\src" >nul
    call npm ci --no-audit --no-fund --prefer-offline --ignore-scripts
    if errorlevel 1 (
        echo  ERROR: npm ci failed while syncing frontend dependencies
        set /a ERRORS+=1
        goto :ERROR_SUMMARY
    )
    popd >nul
    echo  OK - Frontend dependencies synced with package.json
) else (
    echo  WARN - src\package.json not found, skipping npm refresh
)
echo.

ECHO ===================================================================
ECHO STEP 3: Create Missing Config Files
ECHO ===================================================================
echo [3/7] Ensuring config files exist...

if not exist "%PROJECT_PATH%\src\postcss.config.js" (
    echo  Creating postcss.config.js...
    (
        echo export default {};
    ) > "%PROJECT_PATH%\src\postcss.config.js"
    echo  OK - postcss.config.js created
) else (
    echo  OK - postcss.config.js already exists
)

if not exist "%PROJECT_PATH%\src\.env" (
    if exist "%PROJECT_PATH%\src\.env.example" (
        echo  Copying .env from .env.example...
        copy "%PROJECT_PATH%\src\.env.example" "%PROJECT_PATH%\src\.env" >nul
        echo  OK - .env created from template
    ) else (
        echo  WARN - .env.example not found, skipping
    )
) else (
    echo  OK - .env already exists
)

REM --- Puerto LAN (solicitar al usuario) ---
:ASK_LAN_PORT
set "LAN_PORT_INPUT="
set /p "LAN_PORT_INPUT=Enter LAN_PORT (e.g. 8282): "
if not defined LAN_PORT_INPUT goto :ASK_LAN_PORT
set "LAN_PORT=%LAN_PORT_INPUT%"

REM --- Solicitar web (solicitar al usuario) ---
:ASK_APP_URL
set "APP_URL_INPUT="
set /p "APP_URL_INPUT=Enter APP_URL (e.g. http://localhost:8282): "
if not defined APP_URL_INPUT goto :ASK_APP_URL
set "ENV_FILE=%PROJECT_PATH%\src\.env"
powershell -NoProfile -Command "$path = $env:ENV_FILE; $url = $env:APP_URL_INPUT; $content = [IO.File]::ReadAllText($path); if ($content -match '(?m)^APP_URL=.*$') { $content = [regex]::Replace($content, '(?m)^APP_URL=.*$', [System.Text.RegularExpressions.MatchEvaluator]{ param($match) 'APP_URL=' + $url }) } else { $content = $content.TrimEnd() + [Environment]::NewLine + 'APP_URL=' + $url + [Environment]::NewLine }; [IO.File]::WriteAllText($path, $content, [Text.UTF8Encoding]::new($false))"
if errorlevel 1 (
    echo  ERROR: Could not update APP_URL in src\.env
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - APP_URL updated in src\.env
echo.

REM --- Grabar LAN_HOST_IP ---
set "LAN_HOST_IP_VALUE=%LAN_IP%"
powershell -NoProfile -Command "$path = $env:ENV_FILE; $value = $env:LAN_HOST_IP_VALUE; $content = [IO.File]::ReadAllText($path); if ($content -match '(?m)^LAN_HOST_IP=.*$') { $content = [regex]::Replace($content, '(?m)^LAN_HOST_IP=.*$', [System.Text.RegularExpressions.MatchEvaluator]{ param($match) 'LAN_HOST_IP=' + $value }) } else { $content = $content.TrimEnd() + [Environment]::NewLine + 'LAN_HOST_IP=' + $value + [Environment]::NewLine }; [IO.File]::WriteAllText($path, $content, [Text.UTF8Encoding]::new($false))"
if errorlevel 1 (
    echo  ERROR: Could not update LAN_HOST_IP in src\.env
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - LAN_HOST_IP updated in src\.env

REM --- Grabar LAN_PORT ---
powershell -NoProfile -Command "$path = $env:ENV_FILE; $value = $env:LAN_PORT; $content = [IO.File]::ReadAllText($path); if ($content -match '(?m)^LAN_PORT=.*$') { $content = [regex]::Replace($content, '(?m)^LAN_PORT=.*$', [System.Text.RegularExpressions.MatchEvaluator]{ param($match) 'LAN_PORT=' + $value }) } else { $content = $content.TrimEnd() + [Environment]::NewLine + 'LAN_PORT=' + $value + [Environment]::NewLine }; [IO.File]::WriteAllText($path, $content, [Text.UTF8Encoding]::new($false))"
if errorlevel 1 (
    echo  ERROR: Could not update LAN_PORT in src\.env
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - LAN_PORT updated in src\.env

ECHO ===================================================================
ECHO STEP 4: Validate Docker Compose File
ECHO ===================================================================
echo [4/7] Validating docker-compose.yml...
docker compose -f "%PROJECT_PATH%\docker-compose.yml" config >nul 2>&1
if errorlevel 1 (
    echo  ERROR: docker-compose.yml validation failed
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - docker-compose.yml is valid
echo.

REM ===================================================================
REM STEP 4.5: Ensure Windows Firewall allows incoming traffic to nginx port
REM ===================================================================
REM [4.5/7] Ensuring Windows Firewall allows port 8282 (nginx)...
REM powershell -Command "if (-not (Get-NetFirewallRule -DisplayName 'UtpIntegrador nginx' -ErrorAction SilentlyContinue)) { New-NetFirewallRule -DisplayName 'UtpIntegrador nginx' -Direction Inbound -LocalPort 8282 -Protocol TCP -Action Allow; Write-Output 'Firewall rule created' } else { Write-Output 'Firewall rule already exists' }"
REM if errorlevel 1 (
REM     echo  WARN: Could not create firewall rule (requires Administrator privilege)
REM     echo  You may need to run this script as Administrator or create the rule manually:
REM     echo    New-NetFirewallRule -DisplayName "UtpIntegrador nginx" -Direction Inbound -LocalPort 8282 -Protocol TCP -Action Allow
REM ) else (
REM     echo  OK - Firewall rule ensured
REM )
REM echo.

ECHO ===================================================================
ECHO STEP 5: Rebuild Docker Containers
ECHO ===================================================================
echo [5/7] Rebuilding Docker containers...
echo  - Stopping existing containers (preserving database and uploaded assets)...
docker compose -f "%PROJECT_PATH%\docker-compose.yml" down 2>nul

echo  - Starting core services (mysql, redis, app)...
docker compose -f "%PROJECT_PATH%\docker-compose.yml" up -d mysql redis app
if errorlevel 1 (
    echo  ERROR: docker compose up failed for core services
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)

echo  - Ensuring Laravel storage folders and public link exist...
docker exec UtpIntegrador-app sh -lc "mkdir -p /var/www/storage/app/public /var/www/storage/app/public/trabajadores && chmod -R 0775 /var/www/storage /var/www/public && cd /var/www && php artisan storage:link"
if errorlevel 1 (
    echo  ERROR: Laravel storage folders or public symlink could not be created
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)

echo  - Waiting for database to be ready (30 seconds)...
timeout /t 30 /nobreak

echo  - Starting Vite dev server and Nginx...
docker compose -f "%PROJECT_PATH%\docker-compose.yml" --profile dev up -d --force-recreate vite nginx
if errorlevel 1 (
    echo  ERROR: docker compose up failed for vite/nginx
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  - Waiting for services to stabilize (10 seconds)...
timeout /t 10 /nobreak

echo  - Checking Vite dev server...
set "VITE_CHECK=%TEMP%\UtpIntegradortienda-vite-client.txt"
C:\Windows\System32\curl.exe -fsS --retry 15 --retry-delay 2 -o "%VITE_CHECK%" http://localhost:5173/@vite/client
findstr /i "vite" "%VITE_CHECK%" >nul 2>&1
if errorlevel 1 (
    echo  ERROR: Vite is not serving /@vite/client on port 5173
    docker compose -f "%PROJECT_PATH%\docker-compose.yml" logs --tail 40 vite
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
del "%VITE_CHECK%" >nul 2>&1
echo  OK - Vite dev server responds

echo  OK - Containers started
echo.

ECHO ===================================================================
ECHO STEP 6: Verify npm Dependencies
ECHO ===================================================================
ECHO [6/7] Verifying npm dependencies...

docker exec UtpIntegrador-app sh -c "test -f /var/www/node_modules/vite/package.json" >nul 2>&1
if errorlevel 1 (
    echo  ERROR: npm dependencies could not be installed
    docker compose -f "%PROJECT_PATH%\docker-compose.yml" logs --tail 40 app
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
) else (
    echo  OK - npm dependencies available in Docker volume
)

echo.

ECHO ===================================================================
ECHO STEP 6B: Ensure PHP dependencies (incluye chillerlan/php-qrcode)
ECHO ===================================================================
echo [6B/7] Installing PHP dependencies from composer.lock...
docker exec UtpIntegrador-app sh -c "cd /var/www && composer install --no-interaction --prefer-dist"
if errorlevel 1 (
    echo  ERROR: composer install failed
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - PHP dependencies installed
echo.

rem docker compose exec -T app php artisan storage:link

echo.

echo  - Rebuilding frontend assets cleanly...
if exist "%PROJECT_PATH%\src\public\build" (
    rmdir /s /q "%PROJECT_PATH%\src\public\build"
)

docker exec UtpIntegrador-app sh -c "cd /var/www && npm run build" > "%TEMP%\UtpIntegradortienda-build.log" 2>&1
if errorlevel 1 (
    echo  ERROR: Frontend build failed
    echo  --- build output ---
    type "%TEMP%\UtpIntegradortienda-build.log"
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)

docker exec UtpIntegrador-app sh -c "test -f /var/www/public/build/manifest.json" >nul 2>&1
if errorlevel 1 (
    echo  ERROR: Vite manifest missing
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)

if exist "%PROJECT_PATH%\src\public\build\manifest.json" (
    findstr /i /c:"resources/css/app.css" "%PROJECT_PATH%\src\public\build\manifest.json" >nul
    if errorlevel 1 (
        echo  ERROR: Vite manifest missing frontend entry points
        set /a ERRORS+=1
        goto :ERROR_SUMMARY
    )
    findstr /i /c:"resources/js/app.js" "%PROJECT_PATH%\src\public\build\manifest.json" >nul
    if errorlevel 1 (
        echo  ERROR: Vite manifest missing frontend entry points
        set /a ERRORS+=1
        goto :ERROR_SUMMARY
    )
)

if exist "%PROJECT_PATH%\src\public\hot" (
    del "%PROJECT_PATH%\src\public\hot"
    echo  OK - Production assets selected
)
echo  OK - Frontend assets built
echo.

REM ===================================================================
REM STEP 7: Health Check
REM ===================================================================
echo [7/7] Running health checks...

docker ps -f "name=UtpIntegrador-app" --format "{{.Names}}" | find "UtpIntegrador-app" >nul
if errorlevel 1 (
    echo  ERROR: App container not running
    set /a ERRORS+=1
) else (
    echo  OK - App container running
)

docker ps -f "name=UtpIntegrador-vite" --format "{{.Names}}" | find "UtpIntegrador-vite" >nul
if errorlevel 1 (
    echo  WARN - Vite container not running
) else (
    echo  OK - Vite container running
)

C:\Windows\System32\curl.exe -fsS -o nul http://localhost:8282/
if errorlevel 1 (
    echo  ERROR: Landing page health check failed
    set /a ERRORS+=1
) else (
    echo  OK - Landing page responds
)

echo.

REM ===================================================================
REM STEP 8: Reghenetar token y QR
REM ===================================================================
docker compose exec -T -u www-data app php artisan pedidos:regenerar-qr

ECHO ===================================================================
ECHO Final Summary
ECHO ===================================================================
:ERROR_SUMMARY
if not !ERRORS! equ 0 goto :BUILD_FAILED
echo ============================================================
echo  BUILD SUCCESSFUL
echo ============================================================
echo.
echo  Services are running at:
echo    - Frontend: http://localhost:8282
echo    - Vite Dev: http://localhost:5173
echo    - MySQL:    localhost:3307
echo    - Redis:    localhost:6380
echo.
goto :BUILD_END

:BUILD_FAILED
echo ============================================================
echo  BUILD FAILED - !ERRORS! error(s) found
echo ============================================================
echo.
echo  Please review errors above and run rebuild.bat again
echo.
:BUILD_END
popd >nul
endlocal
exit /b !ERRORS!
