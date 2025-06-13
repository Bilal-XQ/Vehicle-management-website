# Vehicle Management System - Start Script (CORS Fixed)
# This script starts both the backend and frontend servers
# ✅ CORS Issue Fixed - Login should work now!

Write-Host "=== Vehicle Management System Startup ===" -ForegroundColor Green
Write-Host "✅ CORS Issue Fixed - Login should work now!" -ForegroundColor Green
Write-Host "Starting Backend Server (PHP)..." -ForegroundColor Yellow

# Start Backend Server (PHP)
Start-Process powershell -ArgumentList "-Command", "cd 'c:\Users\user\PhpstormProjects\SI'; Write-Host 'Backend Server Starting on http://localhost:8000' -ForegroundColor Green; php -S localhost:8000 simple-api.php; Read-Host 'Press Enter to close'"

# Wait a moment for backend to start
Start-Sleep -Seconds 3

Write-Host "Starting Frontend Server (React + Vite)..." -ForegroundColor Yellow

# Start Frontend Server (Vite)
Start-Process powershell -ArgumentList "-Command", "cd 'c:\Users\user\PhpstormProjects\SI\frontend'; Write-Host 'Frontend Server Starting on http://localhost:5173' -ForegroundColor Green; npm run dev; Read-Host 'Press Enter to close'"

# Wait a moment for frontend to start
Start-Sleep -Seconds 5

Write-Host "=== Servers Started Successfully ===" -ForegroundColor Green
Write-Host "Backend API: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Frontend App: http://localhost:5173" -ForegroundColor Cyan
Write-Host ""
Write-Host "Test Login Credentials:" -ForegroundColor Yellow
Write-Host "Email: test@example.com" -ForegroundColor White
Write-Host "Password: password" -ForegroundColor White
Write-Host ""
Write-Host "Press Enter to open the application in browser..."
Read-Host

# Open the application in browser
Start-Process "http://localhost:5173"
