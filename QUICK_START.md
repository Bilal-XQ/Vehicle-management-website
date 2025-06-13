# Vehicle Management System - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- PHP 7.4+ installed and accessible via command line
- Node.js 16+ and npm installed
- Modern web browser

### Starting the Application

#### Option 1: Use the All-in-One Startup Script (Recommended)
Run either of these scripts to start both servers automatically:

**PowerShell:**
```powershell
.\start-app.ps1
```

**Batch File:**
```batch
start-app.bat
```

#### Option 2: Start Servers Manually

**Backend Server (PHP):**
```bash
php -S localhost:8000 -t .
```

**Frontend Server (React + Vite):**
```bash
cd frontend
npm run dev
```

## 🌐 Application URLs

- **Frontend Application**: http://localhost:5173
- **Backend API**: http://localhost:8000

## 🔐 Test Login Credentials

Since this is a development version with a temporary API, you can use any credentials to log in:

- **Email**: `test@example.com` (or any email)
- **Password**: `password` (or any password)

## 📁 Project Structure

```
/
├── simple-api.php          # Temporary PHP API for testing
├── start-app.ps1          # PowerShell startup script
├── start-app.bat          # Batch startup script
├── frontend/              # React + TypeScript frontend
│   ├── src/
│   │   ├── pages/         # React pages (Login, Dashboard, etc.)
│   │   ├── components/    # Reusable React components
│   │   └── services/      # API service layers
│   └── package.json
└── app/                   # Laravel backend (full setup in progress)
```

## 🛠️ Development Status

### ✅ Working Features
- Frontend React application with TypeScript
- Basic login page with form validation
- Responsive design with Tailwind CSS
- Temporary PHP API for authentication testing
- Both servers can start and communicate

### 🚧 In Progress
- Full Laravel backend setup (replacing temporary PHP API)
- Database migrations and models
- Complete CRUD operations for vehicles
- Dashboard with real data

## 🔧 Troubleshooting

### Backend Server Won't Start
1. Ensure PHP is installed: `php -v`
2. Check if port 8000 is free: `netstat -an | findstr :8000`
3. Try starting manually: `php -S localhost:8000`

### Frontend Server Won't Start
1. Ensure Node.js is installed: `node -v`
2. Install dependencies: `cd frontend && npm install`
3. Check if port 5173 is free: `netstat -an | findstr :5173`

### Login Not Working
1. Verify backend server is running on http://localhost:8000
2. Check browser console for CORS errors
3. Ensure the temporary API file `simple-api.php` exists

### CORS Issues
The temporary API includes CORS headers for `http://localhost:5173`. If you're running the frontend on a different port, update the CORS origin in `simple-api.php`.

## 📞 Next Steps

1. **Complete Laravel Setup**: Install Composer dependencies and set up the full Laravel backend
2. **Database Setup**: Configure MySQL and run migrations
3. **Replace Temporary API**: Switch from `simple-api.php` to Laravel API endpoints
4. **Add Real Authentication**: Implement Laravel Sanctum for proper authentication
5. **Build Vehicle Management**: Create CRUD operations for vehicles and maintenance logs

## 🎯 Current Functionality

The application currently provides:
- Modern React frontend with TypeScript
- Working login form with validation
- Responsive design optimized for mobile and desktop
- Basic API communication
- Development environment setup

You can log in with any credentials and explore the frontend interface while we continue building the full Laravel backend.
