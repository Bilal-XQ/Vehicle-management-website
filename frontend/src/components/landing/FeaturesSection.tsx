import React from 'react';
import Card from '../ui/Card';
import { FeatureItemProps } from '../../types/landing.types';
import { motion } from 'framer-motion';
import { 
  TruckIcon, 
  WrenchScrewdriverIcon, 
  ChartBarIcon, 
  DocumentTextIcon, 
  ClipboardDocumentListIcon, 
  UsersIcon 
} from '@heroicons/react/24/outline';

const features: FeatureItemProps[] = [
  {
    icon: <TruckIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'Vehicle Fleet Management',
    description: 'Track and manage your entire fleet in one unified platform with real-time location tracking and status updates.'
  },
  {
    icon: <WrenchScrewdriverIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'Maintenance Scheduling',
    description: 'Automated maintenance reminders and service history tracking to keep your fleet in optimal condition.'
  },
  {
    icon: <ChartBarIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'Fuel Tracking & Analytics',
    description: 'Monitor fuel consumption, efficiency, and costs with detailed analytics and expense forecasting.'
  },
  {
    icon: <DocumentTextIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'Document Management',
    description: 'Secure digital storage for vehicle documents, including registrations, insurance, and maintenance records.'
  },
  {
    icon: <ClipboardDocumentListIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'Real-time Dashboard',
    description: 'Comprehensive dashboard with live statistics, alerts, and reporting for data-driven fleet management.'
  },
  {
    icon: <UsersIcon className="h-8 w-8 text-primary-600 dark:text-primary-400" />,
    title: 'User Role Management',
    description: 'Customizable access levels for administrators, managers, and drivers with role-specific dashboards.'
  }
];

const container = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1
    }
  }
};

const item = {
  hidden: { opacity: 0, y: 20 },
  show: { opacity: 1, y: 0, transition: { duration: 0.5 } }
};

const FeatureItem: React.FC<FeatureItemProps> = ({ icon, title, description }) => {
  return (
    <motion.div variants={item}>
      <Card className="h-full p-6">
        <div className="space-y-4">
          <div className="bg-primary-50 dark:bg-primary-900/20 p-3 rounded-lg inline-block">
            {icon}
          </div>
          <h3 className="text-xl font-semibold text-gray-900 dark:text-white">{title}</h3>
          <p className="text-gray-600 dark:text-gray-300">{description}</p>
        </div>
      </Card>
    </motion.div>
  );
};

const FeaturesSection: React.FC = () => {
  return (
    <section id="features" className="section-padding bg-gray-50 dark:bg-gray-800/50">
      <div className="container-width">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <h2 className="heading-lg text-gray-900 dark:text-white mb-4">
            Comprehensive Fleet Management Features
          </h2>
          <p className="paragraph">
            Our platform provides all the tools you need to efficiently manage your vehicle fleet, from maintenance tracking to fuel analytics.
          </p>
        </div>

        <motion.div 
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          variants={container}
          initial="hidden"
          whileInView="show"
          viewport={{ once: true, amount: 0.2 }}
        >
          {features.map((feature, index) => (
            <FeatureItem
              key={index}
              icon={feature.icon}
              title={feature.title}
              description={feature.description}
            />
          ))}
        </motion.div>
      </div>
    </section>
  );
};

export default FeaturesSection;
