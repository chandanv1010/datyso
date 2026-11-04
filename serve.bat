@echo off
title DATYSO Local Server
set "PATH=C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;%PATH%"
echo ===================================================
echo   DANG KHOI DONG SERVER DATYSO LOCAL...
echo   Truy cap website tai: http://127.0.0.1:8000
echo ===================================================
php artisan serve
pause

