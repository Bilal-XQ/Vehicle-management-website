# Vehicle Management App - Start Script with CORS Testing
Write-Host "🚗 Vehicle Management App - Starting with CORS Testing..." -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Yellow

# Function to check if a port is in use
function Test-Port {
    param([int]$Port)
    try {
        $connection = New-Object System.Net.Sockets.TcpClient("localhost", $Port)
        $connection.Close()
        Write-Host "❌ Port $Port is already in use" -ForegroundColor Red
        return $false
    }
    catch {
        Write-Host "✅ Port $Port is available" -ForegroundColor Green
        return $true
    }
}

# Function to test CORS headers
function Test-CORS {
    Write-Host "🔍 Testing CORS headers..." -ForegroundColor Cyan
    
    # Wait for PHP server to start
    Start-Sleep -Seconds 3
    
    try {
        # Test OPTIONS request (preflight)
        Write-Host "Testing OPTIONS preflight request..." -ForegroundColor Yellow
        $optionsResponse = Invoke-WebRequest -Uri "http://localhost:8000/simple-api.php/api/auth/login" `
            -Method OPTIONS `
            -Headers @{
                "Origin" = "http://localhost:5173"
                "Access-Control-Request-Method" = "POST"
                "Access-Control-Request-Headers" = "Content-Type, Accept"
            } -UseBasicParsing
        
        Write-Host "OPTIONS Response Status: $($optionsResponse.StatusCode)" -ForegroundColor Green
          # Check for CORS headers
        $corsHeaders = @("Access-Control-Allow-Origin", "Access-Control-Allow-Methods", "Access-Control-Allow-Headers")
        foreach ($header in $corsHeaders) {
            if ($optionsResponse.Headers[$header]) {
                Write-Host "✅ $header`: $($optionsResponse.Headers[$header])" -ForegroundColor Green
            } else {
                Write-Host "❌ Missing header: $header" -ForegroundColor Red
            }
        }
        
        Write-Host ""
        Write-Host "Testing actual POST request..." -ForegroundColor Yellow
        $body = @{
            email = "test@example.com"
            password = "password"
        } | ConvertTo-Json
        
        $postResponse = Invoke-WebRequest -Uri "http://localhost:8000/simple-api.php/api/auth/login" `
            -Method POST `
            -Headers @{
                "Origin" = "http://localhost:5173"
                "Content-Type" = "application/json"
                "Accept" = "application/json"
            } `
            -Body $body -UseBasicParsing
        
        Write-Host "POST Response Status: $($postResponse.StatusCode)" -ForegroundColor Green
        $responseData = $postResponse.Content | ConvertFrom-Json
        Write-Host "Login Response: $($responseData.message)" -ForegroundColor Green
        
    }
    catch {
        Write-Host "❌ CORS test failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Check if we're in the correct directory
if (-not (Test-Path "simple-api.php") -or -not (Test-Path "frontend")) {
    Write-Host "❌ Please run this script from the project root directory" -ForegroundColor Red
    exit 1
}

Write-Host "📂 Current directory: $(Get-Location)" -ForegroundColor Cyan

# Check port availability
Write-Host ""
Write-Host "🔍 Checking port availability..." -ForegroundColor Cyan
$port8000Available = Test-Port -Port 8000
$port5173Available = Test-Port -Port 5173

Write-Host ""
Write-Host "🚀 Starting PHP backend server..." -ForegroundColor Yellow

# Start PHP server in background
$phpProcess = Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8000", "simple-api.php" -PassThru -WindowStyle Hidden

# Wait a moment for PHP server to start
Start-Sleep -Seconds 2

# Test if PHP server is running
try {
    $testResponse = Invoke-WebRequest -Uri "http://localhost:8000/simple-api.php/api/vehicles" -UseBasicParsing
    Write-Host "✅ PHP server started successfully on http://localhost:8000" -ForegroundColor Green
}
catch {
    Write-Host "❌ Failed to start PHP server: $($_.Exception.Message)" -ForegroundColor Red
    if ($phpProcess) { $phpProcess.Kill() }
    exit 1
}

# Test CORS headers
Write-Host ""
Test-CORS

Write-Host ""
Write-Host "🎨 Starting React frontend..." -ForegroundColor Yellow
Set-Location frontend

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "📦 Installing dependencies..." -ForegroundColor Cyan
    npm install
}

# Start React development server
Write-Host "🚀 Starting React development server..." -ForegroundColor Yellow
$reactProcess = Start-Process -FilePath "npm" -ArgumentList "run", "dev" -PassThru

Write-Host ""
Write-Host "🎉 Both servers are starting..." -ForegroundColor Green
Write-Host "📱 Frontend: http://localhost:5173" -ForegroundColor Cyan
Write-Host "🔌 Backend API: http://localhost:8000/simple-api.php" -ForegroundColor Cyan
Write-Host ""
Write-Host "✨ Test credentials:" -ForegroundColor Yellow
Write-Host "   Email: demo@demo.com" -ForegroundColor White
Write-Host "   Password: demo123" -ForegroundColor White
Write-Host ""
Write-Host "🛑 Press Ctrl+C to stop both servers" -ForegroundColor Red

# Function to cleanup on exit
function Cleanup {
    Write-Host ""
    Write-Host "🛑 Stopping servers..." -ForegroundColor Yellow
    if ($phpProcess) { 
        try { $phpProcess.Kill() } catch { }
    }
    if ($reactProcess) { 
        try { $reactProcess.Kill() } catch { }
    }
    Write-Host "✅ Servers stopped" -ForegroundColor Green
}

# Set trap to cleanup on script exit
try {
    Register-EngineEvent -SourceIdentifier PowerShell.Exiting -Action { Cleanup }
    
    # Wait for user to stop
    Write-Host "Waiting for user input to stop servers (Press any key to stop)..." -ForegroundColor Gray
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}
finally {
    Cleanup
}
