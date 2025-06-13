#!/bin/bash

# Vehicle Management App - Start Script with CORS Testing
echo "🚗 Vehicle Management App - Starting with CORS Testing..."
echo "=================================================="

# Function to check if a port is in use
check_port() {
    local port=$1
    if netstat -an | grep ":$port " > /dev/null 2>&1; then
        echo "❌ Port $port is already in use"
        return 1
    else
        echo "✅ Port $port is available"
        return 0
    fi
}

# Function to test CORS headers
test_cors() {
    echo "🔍 Testing CORS headers..."
    
    # Test OPTIONS request (preflight)
    echo "Testing OPTIONS preflight request..."
    curl -i -X OPTIONS \
        -H "Origin: http://localhost:5173" \
        -H "Access-Control-Request-Method: POST" \
        -H "Access-Control-Request-Headers: Content-Type, Accept" \
        http://localhost:8000/simple-api.php/api/auth/login
    
    echo ""
    echo "Testing actual POST request..."
    curl -i -X POST \
        -H "Origin: http://localhost:5173" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{"email":"test@example.com","password":"password"}' \
        http://localhost:8000/simple-api.php/api/auth/login
}

# Check if we're in the correct directory
if [ ! -f "simple-api.php" ] || [ ! -d "frontend" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

echo "📂 Current directory: $(pwd)"

# Check port availability
echo ""
echo "🔍 Checking port availability..."
check_port 8000
check_port 5173

echo ""
echo "🚀 Starting PHP backend server..."
# Start PHP server in background
php -S localhost:8000 simple-api.php &
PHP_PID=$!

# Wait a moment for PHP server to start
sleep 2

# Test if PHP server is running
if curl -s http://localhost:8000/simple-api.php/api/vehicles > /dev/null; then
    echo "✅ PHP server started successfully on http://localhost:8000"
else
    echo "❌ Failed to start PHP server"
    kill $PHP_PID 2>/dev/null
    exit 1
fi

# Test CORS headers
echo ""
test_cors

echo ""
echo "🎨 Starting React frontend..."
cd frontend

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Start React development server
echo "🚀 Starting React development server..."
npm run dev &
REACT_PID=$!

echo ""
echo "🎉 Both servers are starting..."
echo "📱 Frontend: http://localhost:5173"
echo "🔌 Backend API: http://localhost:8000/simple-api.php"
echo ""
echo "✨ Test credentials:"
echo "   Email: demo@demo.com"
echo "   Password: demo123"
echo ""
echo "🛑 To stop both servers: Ctrl+C"

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping servers..."
    kill $PHP_PID 2>/dev/null
    kill $REACT_PID 2>/dev/null
    echo "✅ Servers stopped"
    exit 0
}

# Set trap to cleanup on script exit
trap cleanup INT TERM

# Wait for user to stop
wait
