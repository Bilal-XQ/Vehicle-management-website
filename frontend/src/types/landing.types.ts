// General button props
export interface ButtonProps {
  children: React.ReactNode;
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  className?: string;
  onClick?: () => void;
  type?: 'button' | 'submit' | 'reset';
  disabled?: boolean;
  fullWidth?: boolean;
}

// Card component props
export interface CardProps {
  children: React.ReactNode;
  className?: string;
  onClick?: () => void;
}

// Dark mode toggle props
export interface DarkModeToggleProps {
  className?: string;
}

// Feature item props
export interface FeatureItemProps {
  icon: React.ReactElement;
  title: string;
  description: string;
}

// Benefit item props
export interface BenefitItemProps {
  icon: React.ReactElement;
  title: string;
  description: string;
}

// Tech stack item props
export interface TechStackItemProps {
  icon: React.ReactElement;
  title: string;
  description: string;
}

// Navigation link props
export interface NavLinkProps {
  href: string;
  label: string;
  onClick?: () => void;
}
