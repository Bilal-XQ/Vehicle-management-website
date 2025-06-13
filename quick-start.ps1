# Quick CORS Test and Server Start
Write-Host "🚗 Vehicle Management - Quick Start & CORS Test" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Yellow

# Start PHP server
Write-Host "🚀 Starting PHP server..." -ForegroundColor Cyan
$phpJob = Start-Job -ScriptBlock { 
    Set-Location "c:\Users\user\PhpstormProjects\SI"
    php -S localhost:8000 simple-api.php 
}

# Wait for PHP server to start
Start-Sleep -Seconds 3

# Test CORS quickly
Write-Host "🔍 Testing CORS..." -ForegroundColor Cyan
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8000/simple-api.php/api/auth/login" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "Origin" = "http://localhost:5173"
        } `
        -Body '{"email":"test@example.com","password":"password"}'
    
    if ($response.success) {
        Write-Host "✅ CORS Test PASSED - Login API working!" -ForegroundColor Green
        Write-Host "Token received: $($response.data.token)" -ForegroundColor Green
    } else {
        Write-Host "❌ API Error: $($response.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ CORS Test Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎨 Starting React frontend..." -ForegroundColor Cyan
Set-Location frontend

# Start React server
$reactJob = Start-Job -ScriptBlock { 
    Set-Location "c:\Users\user\PhpstormProjects\SI\frontend"
    npm run dev 
}

Write-Host ""
Write-Host "🎉 Both servers starting..." -ForegroundColor Green
Write-Host "📱 Frontend: http://localhost:5173" -ForegroundColor Cyan
Write-Host "🔌 Backend: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "✨ Test Login:" -ForegroundColor Yellow
Write-Host "   Email: demo@demo.com" -ForegroundColor White
Write-Host "   Password: demo123" -ForegroundColor White
Write-Host ""
Write-Host "Press any key to stop servers..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Cleanup
Stop-Job $phpJob -ErrorAction SilentlyContinue
Stop-Job $reactJob -ErrorAction SilentlyContinue
Remove-Job $phpJob -ErrorAction SilentlyContinue
Remove-Job $reactJob -ErrorAction SilentlyContinue
Write-Host "✅ Servers stopped" -ForegroundColor Green
