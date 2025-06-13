import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { 
  TruckIcon, 
  WrenchScrewdriverIcon, 
  BeakerIcon, 
  UserGroupIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/react/24/outline';
import Button from '../components/ui/Button';
import DarkModeToggle from '../components/ui/DarkModeToggle';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

const Dashboard: React.FC = () => {
  const navigate = useNavigate();
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    // Check if user is logged in
    const token = localStorage.getItem('token');
    const userData = localStorage.getItem('user');
    
    if (!token || !userData) {
      navigate('/login');
      return;
    }

    setUser(JSON.parse(userData));
  }, [navigate]);

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    navigate('/login');
  };

  if (!user) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
        <div className="text-lg text-gray-600 dark:text-gray-400">Loading...</div>
      </div>
    );
  }

  const stats = [
    {
      name: 'Total Vehicles',
      value: '124',
      icon: TruckIcon,
      color: 'bg-blue-500',
    },
    {
      name: 'Maintenance Due',
      value: '8',
      icon: WrenchScrewdriverIcon,
      color: 'bg-orange-500',
    },
    {
      name: 'Fuel Reports',
      value: '45',
      icon: BeakerIcon,
      color: 'bg-green-500',
    },
    {
      name: 'Active Users',
      value: '32',
      icon: UserGroupIcon,
      color: 'bg-purple-500',
    },
  ];

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      {/* Header */}
      <header className="bg-white dark:bg-gray-800 shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-6">
            <div>
              <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                FleetPro Dashboard
              </h1>
              <p className="text-gray-600 dark:text-gray-400">
                Welcome back, {user.name}!
              </p>
            </div>
            <div className="flex items-center space-x-4">
              <DarkModeToggle />
              <div className="text-sm text-gray-600 dark:text-gray-400">
                Role: <span className="font-semibold capitalize">{user.role}</span>
              </div>
              <Button
                onClick={handleLogout}
                variant="outline"
                className="flex items-center space-x-2"
              >
                <ArrowRightOnRectangleIcon className="h-4 w-4" />
                <span>Logout</span>
              </Button>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="px-4 py-6 sm:px-0">
          {/* Stats Grid */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
          >
            {stats.map((stat, index) => (
              <motion.div
                key={stat.name}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg"
              >
                <div className="p-5">
                  <div className="flex items-center">
                    <div className="flex-shrink-0">
                      <div className={`${stat.color} p-3 rounded-md`}>
                        <stat.icon className="h-6 w-6 text-white" />
                      </div>
                    </div>
                    <div className="ml-5 w-0 flex-1">
                      <dl>
                        <dt className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                          {stat.name}
                        </dt>
                        <dd className="text-lg font-medium text-gray-900 dark:text-white">
                          {stat.value}
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </motion.div>

          {/* Quick Actions */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.6 }}
            className="mt-8"
          >
            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
              Quick Actions
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <button
                onClick={() => navigate('/vehicles')}
                className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-left group"
              >
                <div className="flex items-center">
                  <div className="bg-blue-500 p-3 rounded-md group-hover:bg-blue-600 transition-colors">
                    <TruckIcon className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <h4 className="text-lg font-medium text-gray-900 dark:text-white">
                      Manage Vehicles
                    </h4>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      View and manage your vehicle fleet
                    </p>
                  </div>
                </div>
              </button>
              
              <button
                onClick={() => console.log('Navigate to maintenance')}
                className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-left group"
              >
                <div className="flex items-center">
                  <div className="bg-yellow-500 p-3 rounded-md group-hover:bg-yellow-600 transition-colors">
                    <WrenchScrewdriverIcon className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <h4 className="text-lg font-medium text-gray-900 dark:text-white">
                      Maintenance Logs
                    </h4>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      Track vehicle maintenance history
                    </p>
                  </div>
                </div>
              </button>
              
              <button
                onClick={() => console.log('Navigate to fuel logs')}
                className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-left group"
              >
                <div className="flex items-center">
                  <div className="bg-green-500 p-3 rounded-md group-hover:bg-green-600 transition-colors">
                    <BeakerIcon className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <h4 className="text-lg font-medium text-gray-900 dark:text-white">
                      Fuel Tracking
                    </h4>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      Monitor fuel consumption and costs
                    </p>
                  </div>
                </div>
              </button>
            </div>
          </motion.div>

          {/* Welcome Message */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.5 }}
            className="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg"
          >
            <div className="px-4 py-5 sm:p-6">
              <h3 className="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                🎉 Authentication System Complete!
              </h3>
              <div className="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                <p>
                  Congratulations! Your Vehicle Management System now has a fully functional authentication system.
                  You're logged in as <strong>{user.name}</strong> with <strong>{user.role}</strong> role.
                </p>
              </div>
              <div className="mt-5">
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                  <h4 className="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                    Next Steps:
                  </h4>                  <ul className="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                    <li>✅ Vehicle management pages (Click "Manage Vehicles" above!)</li>
                    <li>• Add maintenance logging functionality</li>
                    <li>• Implement fuel tracking</li>
                    <li>• Build reporting dashboard</li>
                    <li>• Add user role permissions</li>
                  </ul>
                </div>
              </div>
            </div>
          </motion.div>
        </div>
      </main>
    </div>
  );
};

export default Dashboard;
