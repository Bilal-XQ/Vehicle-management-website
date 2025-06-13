# 🚀 Quick Start Guide - Vehicle Management System

## 🎯 Prerequisites Check

Before running the application, make sure you have:
- ✅ **PHP 7.4+** installed on your system
- ✅ **Node.js 18+** and **npm** installed
- ✅ All dependencies installed

## 📝 Step-by-Step Startup Instructions

### Option 1: Use the PowerShell Script (Recommended)

1. **Open PowerShell as Administrator**
2. **Navigate to the project directory**:
   ```powershell
   cd "C:\Users\user\PhpstormProjects\SI"
   ```
3. **Run the startup script**:
   ```powershell
   .\start-app.ps1
   ```

### Option 2: Manual Startup

#### Step 1: Start Backend Server
1. **Open PowerShell/Command Prompt**
2. **Navigate to project root**:
   ```powershell
   cd "C:\Users\user\PhpstormProjects\SI"
   ```
3. **Start PHP server**:
   ```powershell
   php -S localhost:8000 simple-api.php
   ```
4. **Leave this terminal open** - Backend running on http://localhost:8000

#### Step 2: Start Frontend Server (New Terminal)
1. **Open another PowerShell/Command Prompt**
2. **Navigate to frontend folder**:
   ```powershell
   cd "C:\Users\user\PhpstormProjects\SI\frontend"
   ```
3. **Install dependencies** (first time only):
   ```powershell
   npm install
   ```
4. **Start development server**:
   ```powershell
   npm run dev
   ```
5. **Leave this terminal open** - Frontend running on http://localhost:5173

## 🌐 Access Your Application

### Main URLs:
- **🏠 Frontend Application**: http://localhost:5173
- **🔧 Backend API**: http://localhost:8000
- **🚗 Vehicle Management**: http://localhost:5173/vehicles
- **📊 Dashboard**: http://localhost:5173/dashboard

### Test Login Credentials:
- **Email**: `test@example.com`
- **Password**: `password` (any password works)

## 🧪 Test the Complete Application

### 1. Authentication Test
- Go to http://localhost:5173/login
- Enter any email/password
- Should redirect to dashboard

### 2. Vehicle Management Test
- Navigate to http://localhost:5173/vehicles
- You should see 5 sample vehicles
- Test the search and filter functionality

### 3. Phase 2 Features Test
- Click "Add Vehicle" button to test the add form
- Click edit icons on vehicle cards to test editing
- Click view icons to see detailed vehicle information
- Test form validation by submitting incomplete forms

## 📋 Feature Checklist

After startup, you should be able to:

### ✅ Core Features (Phase 1)
- [ ] Login with any credentials
- [ ] View dashboard with statistics
- [ ] Browse vehicle list with 5 sample vehicles
- [ ] Search vehicles by make/model/license plate
- [ ] Filter vehicles by status, make, year
- [ ] Navigate between pages using pagination

### ✅ Form Management (Phase 2)
- [ ] Click "Add Vehicle" - modal opens
- [ ] Fill and submit new vehicle form
- [ ] Click edit icon - edit modal opens with pre-filled data
- [ ] Click view icon - detailed vehicle information modal
- [ ] Delete vehicles with confirmation
- [ ] See toast notifications for all actions

## 🔧 Troubleshooting

### Backend Issues:
- **Port 8000 in use**: Change to different port like `php -S localhost:8001 simple-api.php`
- **PHP not found**: Install PHP or add to PATH
- **CORS errors**: Check if backend is running on port 8000

### Frontend Issues:
- **Dependencies missing**: Run `npm install` in frontend folder
- **Port 5173 in use**: Vite will automatically use next available port
- **Build errors**: Check Node.js version (needs 18+)

### API Connection Issues:
- Make sure both servers are running
- Check browser console for errors
- Verify API calls in Network tab

## 🎯 Next Steps

Once running successfully:

1. **Test Phase 1 & 2 Features**
2. **Ready for Phase 3: Maintenance System**
3. **Continue with advanced features**

## 🆘 Need Help?

If you encounter issues:
1. Check both terminal windows for error messages
2. Verify PHP and Node.js versions
3. Check if ports 8000 and 5173 are available
4. Look at browser console for JavaScript errors

Your Vehicle Management System should now be running with complete CRUD functionality! 🎉
