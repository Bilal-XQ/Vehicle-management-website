import React from 'react';
import { Vehicle, VehicleStatus } from '../../types/vehicle';
import { 
  PencilIcon, 
  TrashIcon, 
  EyeIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon
} from '@heroicons/react/24/outline';
import Button from '../ui/Button';

interface VehicleCardProps {
  vehicle: Vehicle;
  onEdit: (vehicle: Vehicle) => void;
  onDelete: (id: number) => void;
  onView: (vehicle: Vehicle) => void;
}

const getStatusIcon = (status: VehicleStatus) => {
  switch (status) {
    case VehicleStatus.ACTIVE:
      return <CheckCircleIcon className="w-5 h-5 text-green-500" />;
    case VehicleStatus.MAINTENANCE:
      return <ExclamationTriangleIcon className="w-5 h-5 text-yellow-500" />;
    case VehicleStatus.RETIRED:
      return <ClockIcon className="w-5 h-5 text-gray-500" />;
    default:
      return <CheckCircleIcon className="w-5 h-5 text-green-500" />;
  }
};

const getStatusColor = (status: VehicleStatus) => {
  switch (status) {
    case VehicleStatus.ACTIVE:
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case VehicleStatus.MAINTENANCE:
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    case VehicleStatus.RETIRED:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    default:
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
  }
};

export const VehicleCard: React.FC<VehicleCardProps> = ({
  vehicle,
  onEdit,
  onDelete,
  onView
}) => {
  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6">
      {/* Header */}
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            {vehicle.make} {vehicle.model}
          </h3>
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {vehicle.year} • {vehicle.license_plate}
          </p>
        </div>
        <div className="flex items-center space-x-2">
          {getStatusIcon(vehicle.status)}
          <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(vehicle.status)}`}>
            {vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1)}
          </span>
        </div>
      </div>

      {/* Vehicle Details */}
      <div className="space-y-2 mb-4">
        <div className="flex justify-between text-sm">
          <span className="text-gray-500 dark:text-gray-400">Mileage:</span>
          <span className="text-gray-900 dark:text-white font-medium">
            {vehicle.mileage.toLocaleString()} miles
          </span>
        </div>
        {vehicle.color && (
          <div className="flex justify-between text-sm">
            <span className="text-gray-500 dark:text-gray-400">Color:</span>
            <span className="text-gray-900 dark:text-white font-medium">
              {vehicle.color}
            </span>
          </div>
        )}
        {vehicle.vin && (
          <div className="flex justify-between text-sm">
            <span className="text-gray-500 dark:text-gray-400">VIN:</span>
            <span className="text-gray-900 dark:text-white font-mono text-xs">
              {vehicle.vin}
            </span>
          </div>
        )}
      </div>

      {/* Notes */}
      {vehicle.notes && (
        <div className="mb-4">
          <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded p-2">
            {vehicle.notes.length > 100 
              ? `${vehicle.notes.substring(0, 100)}...` 
              : vehicle.notes
            }
          </p>
        </div>
      )}

      {/* Actions */}
      <div className="flex space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
        <Button
          variant="outline"
          size="sm"
          onClick={() => onView(vehicle)}
          className="flex-1"
        >
          <EyeIcon className="w-4 h-4 mr-1" />
          View
        </Button>
        <Button
          variant="outline"
          size="sm"
          onClick={() => onEdit(vehicle)}
          className="flex-1"
        >
          <PencilIcon className="w-4 h-4 mr-1" />
          Edit
        </Button>
        <Button
          variant="outline"
          size="sm"
          onClick={() => onDelete(vehicle.id)}
          className="flex-1 text-red-600 hover:text-red-700 hover:bg-red-50"
        >
          <TrashIcon className="w-4 h-4 mr-1" />
          Delete
        </Button>
      </div>

      {/* Metadata */}
      <div className="mt-4 text-xs text-gray-400 dark:text-gray-500">
        Added {new Date(vehicle.created_at).toLocaleDateString()}
      </div>
    </div>
  );
};
