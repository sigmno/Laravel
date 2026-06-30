@echo off
setlocal

set DB_NAME=ManufacturingDB
if "%MYSQL_USER%"=="" set MYSQL_USER=root

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd"') do set BACKUP_DATE=%%i
set BACKUP_FILE=%DB_NAME%_backup_%BACKUP_DATE%.sql

mysqldump -u %MYSQL_USER% -p %DB_NAME% > "%BACKUP_FILE%"

if errorlevel 1 (
    echo Backup failed.
    exit /b 1
)

echo Backup created: %BACKUP_FILE%
endlocal
