# FleetPro - Vehicle Management System Frontend

A modern, responsive React-based frontend for the FleetPro Vehicle Management System.

## 🚀 Features

- **Modern Tech Stack**: React 18+, TypeScript, Vite, Tailwind CSS
- **Responsive Design**: Mobile-first design that works on all devices
- **Dark Mode Support**: Toggle between light and dark themes
- **Professional Landing Page**: Showcases features and benefits
- **Type Safety**: Full TypeScript implementation
- **Accessibility**: WCAG 2.1 AA compliant components
- **Modern UI Components**: Reusable component library

## 📦 Tech Stack

- **React 18+** - Modern React with hooks and concurrent features
- **TypeScript** - Type safety and better developer experience
- **Vite** - Fast build tool and development server
- **Tailwind CSS** - Utility-first CSS framework
- **React Router v6** - Client-side routing
- **Heroicons** - Beautiful SVG icons
- **Framer Motion** - Smooth animations and transitions
- **Headless UI** - Unstyled accessible UI components

## 🏗️ Project Structure

```
frontend/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── ui/             # Basic UI elements (Button, Card, etc.)
│   │   ├── layout/         # Layout components (Header, Navigation)
│   │   └── landing/        # Landing page specific components
│   ├── pages/              # Route components
│   ├── hooks/              # Custom React hooks
│   ├── styles/             # Global styles and Tailwind config
│   ├── types/              # TypeScript type definitions
│   └── assets/             # Static assets (images, icons)
├── public/                 # Public assets
├── index.html             # HTML template
├── package.json           # Dependencies and scripts
├── tailwind.config.js     # Tailwind CSS configuration
├── vite.config.ts         # Vite configuration
└── tsconfig.json          # TypeScript configuration
```

## 🚀 Getting Started

### Prerequisites

- Node.js 18+ 
- npm or yarn package manager

### Installation

1. **Clone the repository and navigate to frontend:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Start the development server:**
   ```bash
   npm run dev
   ```

4. **Open your browser:**
   Navigate to `http://localhost:3000` (or the port shown in terminal)

### Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build locally
- `npm run lint` - Run ESLint

## 🎨 Component Library

### UI Components

- **Button** - Versatile button component with multiple variants
- **Card** - Container component with shadow and rounded corners
- **DarkModeToggle** - Theme switcher with persistent preferences

### Layout Components

- **Header** - Responsive header with navigation and mobile menu
- **Navigation** - Accessible navigation with smooth scrolling

### Landing Page Components

- **HeroSection** - Main hero section with CTA buttons
- **FeaturesSection** - Feature showcase with icons and descriptions
- **BenefitsSection** - Benefits overview with illustrations
- **TechStackSection** - Technology stack showcase
- **Footer** - Comprehensive footer with links and contact info

## 🎨 Design System

### Colors

**Primary Colors (Blue):**
- 50: #e6f1ff
- 500: #0072ff (Main brand color)
- 600: #005bcc
- 900: #001733

**Secondary Colors (Green):**
- 50: #e6f7ef
- 500: #00b45f (Accent color)
- 600: #00904c
- 900: #002413

### Typography

- **Font Family**: Inter (Google Fonts)
- **Headings**: Bold with clear hierarchy
- **Body Text**: Readable sizes with proper line height

### Responsive Breakpoints

- **sm**: 640px
- **md**: 768px
- **lg**: 1024px
- **xl**: 1280px
- **2xl**: 1536px

## ♿ Accessibility

- Semantic HTML structure
- ARIA labels and attributes
- Keyboard navigation support
- Color contrast compliance (WCAG 2.1 AA)
- Screen reader friendly

## 🌙 Dark Mode

The app includes a comprehensive dark mode implementation:

- System preference detection
- Persistent user preference storage
- Smooth theme transitions
- Complete component coverage

## 📱 Responsive Design

Mobile-first approach with:

- Flexible grid layouts
- Adaptive typography
- Touch-friendly interactions
- Optimized mobile navigation

## 🔧 Configuration

### Tailwind CSS

Custom color palette and component classes defined in `tailwind.config.js`.

### TypeScript

Strict type checking enabled with comprehensive type definitions.

### Vite

Optimized build configuration with fast HMR and modern bundling.

## 🚀 Deployment

### Build for Production

```bash
npm run build
```

This creates an optimized build in the `dist/` folder ready for deployment.

### Deployment Options

- **Vercel** - Recommended for React apps
- **Netlify** - Great for static site hosting
- **AWS S3 + CloudFront** - Scalable cloud hosting
- **GitHub Pages** - Free hosting for open source

## 🤝 Contributing

1. Follow the established code style and conventions
2. Use TypeScript for all new components
3. Include accessibility attributes
4. Test responsive design on multiple devices
5. Document complex components and functions

## 📄 License

This project is part of the FleetPro Vehicle Management System.

## 🆘 Support

For support and questions:

- Check the documentation
- Review existing issues
- Create a new issue with detailed information

---

Built with ❤️ using modern web technologies for the FleetPro Vehicle Management System.
