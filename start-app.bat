@echo off
echo === Vehicle Management System Startup ===
echo Starting Backend Server (PHP)...

start "Backend Server" cmd /k "cd /d c:\Users\user\PhpstormProjects\SI && echo Backend Server Starting on http://localhost:8000 && php -S localhost:8000 -t ."

timeout /t 3 /nobreak

echo Starting Frontend Server (React + Vite)...

start "Frontend Server" cmd /k "cd /d c:\Users\user\PhpstormProjects\SI\frontend && echo Frontend Server Starting on http://localhost:5173 && npm run dev"

timeout /t 5 /nobreak

echo.
echo === Servers Started Successfully ===
echo Backend API: http://localhost:8000
echo Frontend App: http://localhost:5173
echo.
echo Test Login Credentials:
echo Email: test@example.com
echo Password: password
echo.
echo Press any key to open the application in browser...
pause

start http://localhost:5173
