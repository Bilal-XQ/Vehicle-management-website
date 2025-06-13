import React from 'react';
import { BenefitItemProps } from '../../types/landing.types';
import { motion } from 'framer-motion';
import { 
  BanknotesIcon, 
  ClockIcon, 
  ClipboardDocumentCheckIcon, 
  PresentationChartLineIcon 
} from '@heroicons/react/24/outline';

const benefits: BenefitItemProps[] = [
  {
    icon: <BanknotesIcon className="h-10 w-10 text-secondary-600 dark:text-secondary-400" />,
    title: 'Reduce Operational Costs',
    description: 'Cut fleet expenses by up to 20% through optimized maintenance schedules, fuel efficiency tracking, and reduced vehicle downtime.'
  },
  {
    icon: <ClockIcon className="h-10 w-10 text-secondary-600 dark:text-secondary-400" />,
    title: 'Improve Fleet Uptime',
    description: 'Increase vehicle availability and reliability with proactive maintenance alerts and comprehensive service tracking.'
  },
  {
    icon: <ClipboardDocumentCheckIcon className="h-10 w-10 text-secondary-600 dark:text-secondary-400" />,
    title: 'Ensure Compliance',
    description: 'Stay compliant with transportation regulations, inspection requirements, and documentation with automated reminders.'
  },
  {
    icon: <PresentationChartLineIcon className="h-10 w-10 text-secondary-600 dark:text-secondary-400" />,
    title: 'Data-Driven Decisions',
    description: 'Make informed fleet management decisions based on comprehensive analytics, trends, and performance metrics.'
  }
];

const BenefitItem: React.FC<BenefitItemProps> = ({ icon, title, description }) => {
  return (
    <div className="flex space-x-4">
      <div className="flex-shrink-0">{icon}</div>
      <div>
        <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">{title}</h3>
        <p className="text-gray-600 dark:text-gray-300">{description}</p>
      </div>
    </div>
  );
};

const BenefitsSection: React.FC = () => {
  return (
    <section id="benefits" className="section-padding">
      <div className="container-width">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          {/* Benefits Image */}
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.5 }}
            viewport={{ once: true }}
            className="relative"
          >
            <div className="relative aspect-square lg:aspect-[4/3] bg-gradient-to-tr from-secondary-500/20 to-primary-500/20 rounded-2xl overflow-hidden">
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="text-center p-8">
                  <div className="w-full h-full bg-gray-300 dark:bg-gray-700 rounded-xl opacity-70 animate-pulse">
                    {/* Placeholder for actual benefits image */}
                    <div className="flex items-center justify-center h-full">
                      <p className="text-gray-500 dark:text-gray-400">Benefits Illustration</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </motion.div>

          {/* Benefits Content */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            viewport={{ once: true }}
            className="space-y-8"
          >
            <div className="space-y-4">
              <h2 className="heading-lg text-gray-900 dark:text-white">
                Transform Your Fleet Operations
              </h2>
              <p className="paragraph">
                Our Vehicle Management System delivers tangible benefits that improve your bottom line while enhancing operational efficiency.
              </p>
            </div>

            <div className="space-y-8">
              {benefits.map((benefit, index) => (
                <BenefitItem
                  key={index}
                  icon={benefit.icon}
                  title={benefit.title}
                  description={benefit.description}
                />
              ))}
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default BenefitsSection;
