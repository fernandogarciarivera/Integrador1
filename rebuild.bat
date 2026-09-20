@echo off
REM ===================================================================
REM PEKETIENDA PROJECT REBUILD - Comprehensive Setup Script
REM ===================================================================
REM This script validates and rebuilds the entire project environment

setlocal enabledelayedexpansion
set "PROJECT_PATH=%~dp0"
if "%PROJECT_PATH:~-1%"=="\" set "PROJECT_PATH=%PROJECT_PATH:~0,-1%"
pushd "%PROJECT_PATH%" >nul
set "ERRORS=0"

echo.
echo ============================================================
echo  Project Validation and Rebuild
echo ============================================================
echo.

REM ===================================================================
REM STEP 1: Validate Docker Installation
REM ===================================================================
echo [1/7] Validating Docker installation...
docker --version >nul 2>&1
if errorlevel 1 (
    echo  ERROR: Docker is not installed or not in PATH
    set /a ERRORS+=1
    goto :ERROR_SUMMARY
)
echo  OK - Docker found
echo.

REM ===================================================================
REM STEP 2: Validate Project Structure
REM ===================================================================
echo [2/7] Validating project structure...
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

REM ===================================================================
REM STEP 3A: Validate and refresh frontend dependencies from the lockfile
REM ===================================================================
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

REM ===================================================================
REM STEP 3: Create Missing Config Files
REM ===================================================================
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
echo.

REM ===================================================================
REM STEP 4: Validate Docker Compose File
REM ===================================================================
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
REM STEP 5: Rebuild Docker Containers
REM ===================================================================
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

REM ===================================================================
REM STEP 6: Verify npm Dependencies
REM ===================================================================
echo [6/7] Verifying npm dependencies...

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
REM Final Summary
REM ===================================================================
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
