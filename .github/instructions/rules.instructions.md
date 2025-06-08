---
applyTo: '**'
---

# Modern Vehicle Management App - Project Guidelines

## 🏗️ Architecture & Structure

### Frontend (React + TypeScript + Vite)
- **Framework**: React 18+ with TypeScript for type safety
- **Build Tool**: Vite for fast development and modern bundling
- **State Management**: React hooks + Context API for global state
- **Routing**: React Router v6 for SPA navigation
- **HTTP Client**: Axios for API communication with interceptors

### Backend (Laravel API)
- **Framework**: Laravel 10+ (latest LTS) as API-only backend
- **Authentication**: Laravel Sanctum for SPA token-based auth
- **Database**: MySQL with Eloquent ORM
- **Validation**: Form Request classes for API validation
- **API Design**: RESTful endpoints with consistent JSON responses

### Database Design
- **Soft Deletes**: Use soft deletes for vehicles (deleted_at column)
- **Timestamps**: Always include created_at and updated_at
- **Foreign Keys**: Properly constrained relationships
- **Indexing**: Index frequently queried columns (license_plate, status)

## 📁 Directory Structure

### Frontend Structure
```
/frontend/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── ui/             # Basic UI elements (Button, Input, Modal)
│   │   ├── forms/          # Form components
│   │   └── layout/         # Layout components (Header, Sidebar)
│   ├── hooks/              # Custom React hooks
│   ├── pages/              # Route components
│   ├── services/           # API service layers
│   ├── types/              # TypeScript type definitions
│   ├── utils/              # Utility functions
│   └── styles/             # Tailwind config & global styles
```

### Backend Structure
```
/backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # API controllers
│   │   ├── Requests/       # Form validation classes
│   │   └── Resources/      # API resource transformers
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/           # Sample data
└── routes/
    └── api.php            # API routes only
```

## 🎨 UI/UX Standards

### Tailwind CSS Guidelines
- **Utility-First**: Use Tailwind utilities over custom CSS
- **Component Classes**: Extract repeated patterns into component classes
- **Responsive Design**: Mobile-first approach with responsive breakpoints
- **Dark Mode**: Implement toggle with `dark:` prefix classes
- **Color Palette**: Consistent color scheme throughout the app

### Component Design Principles
- **Reusability**: Build generic, configurable components
- **Accessibility**: Use semantic HTML and ARIA attributes
- **Composition**: Favor composition over inheritance
- **Props Interface**: Well-defined TypeScript interfaces for all props

### UI Component Library
- **Headless UI**: For accessible modals, dropdowns, and form elements
- **Chart Library**: Use Chart.js or Recharts for data visualization
- **Icons**: Heroicons or Lucide React for consistent iconography
- **Animations**: Framer Motion for smooth transitions (optional)

## 🔐 Authentication & Authorization

### Frontend Auth
- **Token Storage**: Store tokens in httpOnly cookies or secure localStorage
- **Route Guards**: Protected routes component for admin-only access
- **Auto-Logout**: Handle token expiration gracefully
- **Auth Context**: Global authentication state management

### Backend Auth
- **Sanctum Setup**: Configure for SPA authentication
- **Middleware**: Apply auth:sanctum to protected routes
- **CSRF Protection**: Proper CSRF handling for SPA
- **Rate Limiting**: API rate limiting for security

## 📊 Data Management

### API Design
- **RESTful**: Follow REST conventions for endpoints
- **Pagination**: Use Laravel's built-in pagination for lists
- **Filtering**: Support query parameters for filtering/searching
- **Error Handling**: Consistent error response format
- **Validation**: Server-side validation with detailed error messages

### Frontend Data Flow
- **API Services**: Centralized API calls in service modules
- **Error Handling**: Global error handling with toast notifications
- **Loading States**: Proper loading indicators for async operations
- **Caching**: Implement basic caching for frequently accessed data

## 🧪 Code Quality Standards

### TypeScript Standards
- **Strict Mode**: Enable strict TypeScript configuration
- **Type Definitions**: Define interfaces for all data structures
- **Generic Types**: Use generics for reusable components
- **Enum Usage**: Use enums for status values and constants

### PHP/Laravel Standards
- **PSR Standards**: Follow PSR-1, PSR-2, PSR-4 coding standards
- **Type Declarations**: Use PHP 8+ type declarations
- **Resource Classes**: Use API resources for consistent response formatting
- **Service Layer**: Extract business logic from controllers

### General Coding Practices
- **DRY Principle**: Don't repeat yourself - extract common functionality
- **SOLID Principles**: Follow SOLID design principles
- **Comments**: Document complex business logic
- **Error Handling**: Comprehensive error handling at all levels

## 🗃️ Database Guidelines

### Vehicle Model Structure
- **Fields**: make, model, year, license_plate, status, mileage
- **Status Enum**: 'active', 'maintenance', 'retired'
- **Relationships**: HasMany maintenance logs
- **Validation**: Unique license plates, year ranges

### Maintenance Logs
- **Fields**: vehicle_id, description, date, cost, mileage_at_service
- **Relationships**: BelongsTo Vehicle
- **Timestamps**: Include performed_at datetime

### Seeders & Migrations
- **Sample Data**: Create realistic test data in seeders
- **Migration Order**: Proper foreign key constraint ordering
- **Rollback Safety**: Ensure migrations can be rolled back

## 🚀 Development Workflow

### Git Practices
- **Branch Strategy**: Feature branches from main/develop
- **Commit Messages**: Conventional commits format
- **Pull Requests**: Required for all changes
- **Code Review**: Peer review before merging

### Environment Setup
- **Docker**: Optional containerization for consistent development
- **Environment Files**: Separate .env files for different environments
- **Hot Reload**: Enable hot reload for both frontend and backend development

### Testing Strategy
- **Frontend**: Jest + React Testing Library for component tests
- **Backend**: PHPUnit for feature and unit tests
- **E2E**: Cypress or Playwright for end-to-end testing (optional)

## 📦 Deployment Guidelines

### Frontend Deployment
- **Static Hosting**: Vercel or Netlify for frontend
- **Environment Variables**: Proper env var management
- **Build Optimization**: Tree shaking and code splitting

### Backend Deployment
- **API Hosting**: Render, Railway, or Laravel Forge
- **Database**: MySQL on cloud provider
- **Environment**: Production environment configuration
- **HTTPS**: SSL certificates for API endpoints

## 🔧 Performance Guidelines

### Frontend Performance
- **Code Splitting**: Route-based code splitting
- **Lazy Loading**: Lazy load non-critical components
- **Image Optimization**: Optimize images and use modern formats
- **Bundle Analysis**: Regular bundle size monitoring

### Backend Performance
- **Database Queries**: Use eager loading to prevent N+1 queries
- **Caching**: Redis for API response caching
- **Indexing**: Proper database indexing strategy
- **API Response**: Minimize API response sizes

## 📝 Documentation Requirements

### Code Documentation
- **README**: Comprehensive setup and usage instructions
- **API Documentation**: Document all API endpoints
- **Component Documentation**: Document complex components
- **Environment Setup**: Step-by-step local development setup

### Project Documentation
- **Architecture Diagrams**: Visual representation of system architecture
- **Database Schema**: ER diagrams and table documentation
- **Deployment Guide**: Production deployment instructions
- **Screenshots**: UI screenshots and feature demonstrations

## 🎯 Feature-Specific Guidelines

### Vehicle Management
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **Pagination**: Server-side pagination for vehicle listings
- **Search/Filter**: By make, model, status, year
- **Validation**: Comprehensive form validation

### Maintenance Logs
- **Date Handling**: Proper timezone handling for dates
- **File Uploads**: Support for maintenance photos/documents (optional)
- **Export**: CSV/PDF export functionality (optional)
- **Reporting**: Basic maintenance reporting

### Dashboard
- **Real-time Data**: Live statistics updates
- **Charts**: Interactive charts with Chart.js/Recharts
- **Responsive**: Mobile-friendly dashboard layout
- **Customization**: Configurable dashboard widgets (optional)

These guidelines ensure consistent, maintainable, and scalable code throughout the Modern Vehicle Management App project.