import React, { useState, useEffect } from 'react';
import { VehicleFilters, VehicleStatus, VehicleMake } from '../../types/vehicle';
import { vehicleService } from '../../services/vehicleApiService';
import { 
  MagnifyingGlassIcon,
  FunnelIcon,
  XMarkIcon
} from '@heroicons/react/24/outline';
import Button from '../ui/Button';

interface VehicleFiltersComponentProps {
  filters: VehicleFilters;
  onFiltersChange: (filters: VehicleFilters) => void;
  onClearFilters: () => void;
}

export const VehicleFiltersComponent: React.FC<VehicleFiltersComponentProps> = ({
  filters,
  onFiltersChange,
  onClearFilters
}) => {
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [makes, setMakes] = useState<VehicleMake[]>([]);
  const [localFilters, setLocalFilters] = useState<VehicleFilters>(filters);

  useEffect(() => {
    loadMakes();
  }, []);

  useEffect(() => {
    setLocalFilters(filters);
  }, [filters]);

  const loadMakes = async () => {
    try {
      const vehicleMakes = await vehicleService.getVehicleMakes();
      setMakes(vehicleMakes);
    } catch (error) {
      console.error('Failed to load vehicle makes:', error);
    }
  };

  const handleInputChange = (key: keyof VehicleFilters, value: string | number) => {
    const newFilters = {
      ...localFilters,
      [key]: value === '' ? undefined : value
    };
    setLocalFilters(newFilters);
    onFiltersChange(newFilters);
  };

  const handleClear = () => {
    setLocalFilters({});
    onClearFilters();
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 30 }, (_, i) => currentYear - i);

  const hasActiveFilters = Object.values(localFilters).some(value => 
    value !== undefined && value !== '' && value !== null
  );

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
      {/* Search Bar */}
      <div className="flex items-center space-x-4 mb-4">
        <div className="flex-1 relative">
          <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search vehicles by make, model, license plate..."
            value={localFilters.search || ''}
            onChange={(e) => handleInputChange('search', e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                     focus:ring-2 focus:ring-blue-500 focus:border-transparent
                     placeholder-gray-500 dark:placeholder-gray-400"
          />
        </div>
        
        <Button
          variant="outline"
          onClick={() => setShowAdvanced(!showAdvanced)}
          className="flex items-center"
        >
          <FunnelIcon className="w-4 h-4 mr-2" />
          Filters
          {hasActiveFilters && (
            <span className="ml-2 bg-blue-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">
              {Object.values(localFilters).filter(v => v !== undefined && v !== '').length}
            </span>
          )}
        </Button>

        {hasActiveFilters && (
          <Button
            variant="outline"
            onClick={handleClear}
            className="flex items-center text-red-600 hover:text-red-700"
          >
            <XMarkIcon className="w-4 h-4 mr-1" />
            Clear
          </Button>
        )}
      </div>

      {/* Advanced Filters */}
      {showAdvanced && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          {/* Make Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Make
            </label>
            <select
              value={localFilters.make || ''}
              onChange={(e) => handleInputChange('make', e.target.value)}
              className="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Makes</option>
              {makes.map((make) => (
                <option key={make.id} value={make.name}>
                  {make.name}
                </option>
              ))}
            </select>
          </div>

          {/* Status Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Status
            </label>
            <select
              value={localFilters.status || ''}
              onChange={(e) => handleInputChange('status', e.target.value as VehicleStatus)}
              className="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Statuses</option>
              <option value={VehicleStatus.ACTIVE}>Active</option>
              <option value={VehicleStatus.MAINTENANCE}>Maintenance</option>
              <option value={VehicleStatus.RETIRED}>Retired</option>
            </select>
          </div>

          {/* Year From */}
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Year From
            </label>
            <select
              value={localFilters.year_from || ''}
              onChange={(e) => handleInputChange('year_from', parseInt(e.target.value))}
              className="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Any Year</option>
              {years.map((year) => (
                <option key={year} value={year}>
                  {year}
                </option>
              ))}
            </select>
          </div>

          {/* Year To */}
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Year To
            </label>
            <select
              value={localFilters.year_to || ''}
              onChange={(e) => handleInputChange('year_to', parseInt(e.target.value))}
              className="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Any Year</option>
              {years.map((year) => (
                <option key={year} value={year}>
                  {year}
                </option>
              ))}
            </select>
          </div>
        </div>
      )}
    </div>
  );
};
