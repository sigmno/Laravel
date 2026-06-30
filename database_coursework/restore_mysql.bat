@echo off
setlocal

set DB_NAME=ManufacturingDB
if "%MYSQL_USER%"=="" set MYSQL_USER=root

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd"') do set BACKUP_DATE=%%i
set BACKUP_FILE=%~1
if "%BACKUP_FILE%"=="" set BACKUP_FILE=%DB_NAME%_backup_%BACKUP_DATE%.sql

if not exist "%BACKUP_FILE%" (
    echo Backup file not found: %BACKUP_FILE%
    exit /b 1
)

mysql -u %MYSQL_USER% -p -e "DROP DATABASE IF EXISTS %DB_NAME%; CREATE DATABASE %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo Database recreation failed.
    exit /b 1
)

mysql -u %MYSQL_USER% -p %DB_NAME% < "%BACKUP_FILE%"
if errorlevel 1 (
    echo Restore failed.
    exit /b 1
)

echo Database restored from: %BACKUP_FILE%
endlocal
