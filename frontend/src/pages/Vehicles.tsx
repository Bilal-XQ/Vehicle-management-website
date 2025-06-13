import React from 'react';
import { VehicleList } from '../components/vehicles/VehicleList';

const Vehicles: React.FC = () => {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <VehicleList />
      </div>
    </div>
  );
};

export default Vehicles;
