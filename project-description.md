Project Title: Modern Vehicle Management App

📋 Project Overview
Build a streamlined vehicle-management web application showcasing a modern React + Tailwind CSS frontend talking to a Laravel API backend. You’ll demonstrate full-stack skills with a clean code structure, type safety, and a sleek, responsive UI.

🛠️ Tech Stack
Layer	Technology
Frontend	React (TypeScript) + Vite
Styling	Tailwind CSS
State & Data	React Router, Axios
Backend	PHP Laravel (latest LTS)
Auth	Laravel Sanctum (SPA token-based auth)
Database	MySQL
Hosting	Vercel or Netlify (frontend), Render or Laravel Forge (backend)

🚀 Core Features (MVP)
Authentication

SPA login/logout via Laravel Sanctum

Protected routes in React for admins only

Vehicle Management (CRUD)

List View: Paginated table of vehicles (make, model, year, license plate, status)

Create/Edit Forms: Modal or page forms with validation

Delete: Soft-delete with confirmation

Maintenance Logs

Per-vehicle log list (date picker + description)

Add new maintenance record inline or in a modal

Dashboard

Quick stats cards (Total Vehicles, In-Service, Maintenance Due)

Bar or donut chart (e.g., vehicles by status) using a lightweight chart library

🎨 UI / UX Guidelines
Component-driven: Build reusable UI components (buttons, cards, tables).

Tailwind CSS:

Use utility classes for rapid layout (flex/grid, spacing, typography).

Implement dark/light mode toggle with Tailwind’s class strategy.

Responsive design: Ensure all pages work on mobile/tablet.

Modals & Notifications: Use a small headless UI library (e.g. Headless UI) for accessible dialogs and toasts.

🗂️ Code Structure
bash
Copy
Edit
/frontend
  /src
    /components
    /hooks
    /pages
    /services      // API wrappers with Axios
    /styles        // Tailwind config, global styles

/backend
  /app
    /Http
      /Controllers
      /Requests     // Form validation
    /Models
  /database
    /migrations
    /seeders
  /routes
    api.php
Git: Commit early & often; write clear commit messages.

README:

Project description & screenshots

Local setup (env vars, migrations, seeding)

Deployment steps

📦 Deliverables for GitHub
Full repo with two folders (frontend/, backend/)

README.md with setup, usage, and feature list

SQL migration & seeder files

Sample .env.example for both apps

A few annotated screenshots or GIFs in a docs/ folder