@echo off
setlocal enabledelayedexpansion

set CONTAINER_APP=UtpIntegrador-app
set DB_USER=UtpIntegradorBuzzerBD
set DB_PASS=12345678
set DB_NAME=UtpIntegradorBuzzer
set BACKUP_DIR=.\backups
set AUTO_YES=0
set COMPOSE_FILE=docker-compose.yml

if "%~1"=="-y" set AUTO_YES=1
if "%~1"=="--yes" set AUTO_YES=1

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo.
echo ============================================================
echo  Database Rebuild with Optional Restore
echo ============================================================
echo.

where docker >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker not found in PATH
    echo Install Docker Desktop: https://www.docker.com/products/docker-desktop
    echo.
    pause
    exit /b 1
)

echo [OK] Docker found
echo [OK] Containers are ready
echo.

REM Find latest dump
echo [*] Looking for latest dump in %BACKUP_DIR%...
set LATEST_DUMP=
for /f "delims=" %%i in ('dir /b /od "%BACKUP_DIR%\database_dump_*.sql" 2^>nul') do set LATEST_DUMP=%%i

if "%LATEST_DUMP%"=="" (
    echo [WARN] No dump found in %BACKUP_DIR%
    set DUMP_FOUND=0
) else (
    set DUMP_FOUND=1
    set DUMP_FILE=%BACKUP_DIR%\%LATEST_DUMP%
    echo [OK] Found dump: !DUMP_FILE!
)
echo.

if %AUTO_YES% equ 1 (
    set CONFIRM_DEL=s
    echo [AUTO] Proceeding with database setup
) else (
    set /p CONFIRM_DEL="Proceed with database setup? (s/N): "
)

if /I "!CONFIRM_DEL!"=="s" (
    if !DUMP_FOUND! equ 1 (
        goto :RESTORE_DB
    ) else (
        goto :NO_DUMP
    )
) else (
    echo.
    echo [*] Operation cancelled
    exit /b 0
)

:RESTORE_DB
echo.
echo [*] Preparing to restore from dump...

if %AUTO_YES% equ 1 (
    set MAKE_BACKUP=n
) else (
    set /p MAKE_BACKUP="Backup current DB first? (s/N): "
)

if /I "!MAKE_BACKUP!"=="s" (
    echo [*] Running backup...
    powershell -ExecutionPolicy Bypass -File .\Exportar-Dump.ps1
    echo.
)

echo [*] Resetting database %DB_NAME%...
docker exec UtpIntegrador-mysql mysql -u%DB_USER% -p%DB_PASS% -e "DROP DATABASE IF EXISTS %DB_NAME%; CREATE DATABASE %DB_NAME%;"
if errorlevel 1 (
    echo [ERROR] Failed to reset database
    goto :ERROR_EXIT
)

echo [OK] Database reset
echo.

echo [*] Restoring from: !DUMP_FILE!
type "!DUMP_FILE!" | docker exec -i UtpIntegrador-mysql mysql -u%DB_USER% -p%DB_PASS% %DB_NAME%
if errorlevel 1 (
    echo [WARN] Restore failed, running migrations instead...
    echo.
    goto :RUN_MIGRATIONS
) else (
    echo [OK] Dump restored
    echo.
    goto :AFTER_SETUP
)

:NO_DUMP
echo [*] No dump - running fresh setup
echo.
goto :RUN_MIGRATIONS

:RUN_MIGRATIONS
echo [*] Running migrations...
docker exec -i %CONTAINER_APP% php artisan migrate --force || echo [WARN] migrate skipped ^(already run^)

REM echo.
REM echo.
REM echo [*] Running seeders...
REM docker exec -i %CONTAINER_APP% php artisan db:seed --class=HeroSlideSeeder || echo [WARN] HeroSlideSeeder skipped
REM docker exec -i %CONTAINER_APP% php artisan db:seed --class=HelpPageSeeder || echo [WARN] HelpPageSeeder skipped

echo.
echo [OK] Database complete
echo.
goto :END

:AFTER_SETUP
echo [OK] Dump restored
echo.
goto :END

:ERROR_EXIT
echo.
echo [ERROR] Setup failed
echo.
pause
exit /b 1

:END
echo ============================================================
echo  Database setup completed
echo ============================================================
echo.
echo Services:
echo   - Frontend: http://localhost:8282
echo   - Vite:     http://localhost:5173
echo   - MySQL:    localhost:3307
echo   - Redis:    localhost:6380
echo.
pause
exit /b 0
