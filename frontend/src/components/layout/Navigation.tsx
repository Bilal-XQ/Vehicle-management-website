import React from 'react';
import { NavLinkProps } from '../../types/landing.types';

interface NavigationProps {
  className?: string;
  isMobile?: boolean;
}

const navLinks: NavLinkProps[] = [
  { href: '#features', label: 'Features' },
  { href: '#benefits', label: 'Benefits' },
  { href: '#technology', label: 'Technology' },
  { href: '#contact', label: 'Contact' },
];

const Navigation: React.FC<NavigationProps> = ({ 
  className = '',
  isMobile = false 
}) => {
  const handleClick = (href: string) => {
    // Smooth scroll to section
    const element = document.querySelector(href);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <nav className={className}>
      {navLinks.map((link) => (
        <a
          key={link.label}
          href={link.href}
          onClick={(e) => {
            e.preventDefault();
            handleClick(link.href);
          }}
          className={`font-medium ${
            isMobile
              ? 'block py-2 text-base'
              : 'text-sm'
          } text-gray-700 hover:text-primary-600 dark:text-gray-200 dark:hover:text-primary-400 transition-theme`}
        >
          {link.label}
        </a>
      ))}
    </nav>
  );
};

export default Navigation;
