import React from 'react';
import { CardProps } from '../../types/landing.types';

const Card: React.FC<CardProps> = ({
  children,
  className = '',
  onClick,
}) => {
  return (
    <div
      className={`bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden transition-theme ${
        onClick ? 'cursor-pointer hover:shadow-lg' : ''
      } ${className}`}
      onClick={onClick}
    >
      {children}
    </div>
  );
};

export default Card;
