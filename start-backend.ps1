# PowerShell script to start the backend server
Write-Host "Starting PHP Development Server on localhost:8000..." -ForegroundColor Green
Set-Location "c:\Users\user\PhpstormProjects\SI"
php -S localhost:8000
