import React from 'react';
import { Link } from 'react-router-dom';
import Button from '../ui/Button';
import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { motion } from 'framer-motion';

const HeroSection: React.FC = () => {
  return (
    <section className="pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
      <div className="container-width">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          {/* Hero Content */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="space-y-6"
          >
            <h1 className="heading-xl text-gray-900 dark:text-white">
              Intelligent Fleet Management <span className="text-primary-600 dark:text-primary-400">Solution</span>
            </h1>
            <p className="paragraph text-xl md:text-2xl font-normal">
              Streamline your vehicle operations, reduce costs, and improve efficiency with our comprehensive fleet management system.
            </p>            <div className="flex flex-wrap gap-4 pt-4">
              <Button size="lg" onClick={() => console.log('Get Started clicked')}>
                Get Started <ArrowRightIcon className="ml-2 h-5 w-5" />
              </Button>
              <Link to="/login">
                <Button variant="outline" size="lg">
                  Watch Demo
                </Button>
              </Link>
            </div>
            <div className="pt-6">
              <div className="flex items-center space-x-4">
                <div className="flex -space-x-2">
                  {[1, 2, 3, 4].map(index => (
                    <div 
                      key={index}
                      className="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 overflow-hidden"
                    />
                  ))}
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-900 dark:text-white">Trusted by 500+ companies</p>
                  <div className="flex items-center">
                    {[1, 2, 3, 4, 5].map(star => (
                      <svg key={star} className="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    ))}
                    <span className="ml-1 text-sm text-gray-600 dark:text-gray-300">4.9/5</span>
                  </div>
                </div>
              </div>
            </div>
          </motion.div>

          {/* Hero Image */}
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="relative"
          >
            <div className="relative aspect-[4/3] bg-gradient-to-br from-primary-500/20 to-secondary-500/20 rounded-2xl overflow-hidden">
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="text-center p-8">
                  <div className="w-full h-full bg-gray-300 dark:bg-gray-700 rounded-xl opacity-70 animate-pulse">
                    {/* Placeholder for actual hero image */}
                    <div className="flex items-center justify-center h-full">
                      <p className="text-gray-500 dark:text-gray-400">Vehicle Dashboard Image</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default HeroSection;
