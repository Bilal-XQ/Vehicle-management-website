import React, { useEffect } from 'react';
import Header from '../components/layout/Header';
import HeroSection from '../components/landing/HeroSection';
import FeaturesSection from '../components/landing/FeaturesSection';
import BenefitsSection from '../components/landing/BenefitsSection';
import TechStackSection from '../components/landing/TechStackSection';
import Footer from '../components/landing/Footer';

const Landing: React.FC = () => {
  // Initialize dark mode based on user preference
  useEffect(() => {
    // Check for saved theme preference or use the user's system preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, []);
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        <HeroSection />
        <FeaturesSection />
        <BenefitsSection />
        <TechStackSection />
      </main>
      
      <Footer />
    </div>
  );
};

export default Landing;
