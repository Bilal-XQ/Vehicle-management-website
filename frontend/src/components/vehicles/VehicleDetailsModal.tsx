import React from 'react';
import { Vehicle, VehicleStatus } from '../../types/vehicle';
import Modal from '../ui/Modal';
import Button from '../ui/Button';
import { 
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon,
  CalendarIcon,
  IdentificationIcon,
  ClipboardDocumentListIcon
} from '@heroicons/react/24/outline';

interface VehicleDetailsModalProps {
  isOpen: boolean;
  onClose: () => void;
  vehicle: Vehicle | null;
  onEdit?: (vehicle: Vehicle) => void;
  onDelete?: (id: number) => void;
}

const getStatusConfig = (status: VehicleStatus) => {
  switch (status) {
    case VehicleStatus.ACTIVE:
      return {
        icon: <CheckCircleIcon className="w-5 h-5" />,
        className: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        label: 'Active'
      };
    case VehicleStatus.MAINTENANCE:
      return {
        icon: <ExclamationTriangleIcon className="w-5 h-5" />,
        className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        label: 'In Maintenance'
      };
    case VehicleStatus.RETIRED:
      return {
        icon: <ClockIcon className="w-5 h-5" />,
        className: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        label: 'Retired'
      };
    default:
      return {
        icon: <CheckCircleIcon className="w-5 h-5" />,
        className: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        label: 'Active'
      };
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatMileage = (mileage: number) => {
  return new Intl.NumberFormat('en-US').format(mileage);
};

export const VehicleDetailsModal: React.FC<VehicleDetailsModalProps> = ({
  isOpen,
  onClose,
  vehicle,
  onEdit,
  onDelete
}) => {
  if (!vehicle) return null;

  const statusConfig = getStatusConfig(vehicle.status);

  const footer = (
    <>
      {onEdit && (
        <Button
          variant="outline"
          onClick={() => onEdit(vehicle)}
        >
          Edit Vehicle
        </Button>
      )}
      {onDelete && (
        <Button
          variant="danger"
          onClick={() => {
            if (window.confirm('Are you sure you want to delete this vehicle?')) {
              onDelete(vehicle.id);
              onClose();
            }
          }}
        >
          Delete Vehicle
        </Button>
      )}
      <Button onClick={onClose}>
        Close
      </Button>
    </>
  );

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={`${vehicle.year} ${vehicle.make} ${vehicle.model}`}
      size="lg"
      footer={footer}
    >
      <div className="space-y-6">
        {/* Status Badge */}
        <div className="flex items-center space-x-2">
          <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusConfig.className}`}>
            {statusConfig.icon}
            <span className="ml-2">{statusConfig.label}</span>
          </span>
        </div>

        {/* Basic Information */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-3">
              Vehicle Information
            </h4>
            <dl className="space-y-3">
              <div>
                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Make</dt>
                <dd className="text-sm text-gray-900 dark:text-white">{vehicle.make}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Model</dt>
                <dd className="text-sm text-gray-900 dark:text-white">{vehicle.model}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Year</dt>
                <dd className="text-sm text-gray-900 dark:text-white">{vehicle.year}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">License Plate</dt>
                <dd className="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                  {vehicle.license_plate}
                </dd>
              </div>
            </dl>
          </div>

          <div>
            <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-3">
              Additional Details
            </h4>
            <dl className="space-y-3">
              <div>
                <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Mileage</dt>
                <dd className="text-sm text-gray-900 dark:text-white">
                  {formatMileage(vehicle.mileage)} miles
                </dd>
              </div>
              {vehicle.color && (
                <div>
                  <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Color</dt>
                  <dd className="text-sm text-gray-900 dark:text-white">{vehicle.color}</dd>
                </div>
              )}
              {vehicle.vin && (
                <div>
                  <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">VIN</dt>
                  <dd className="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded break-all">
                    {vehicle.vin}
                  </dd>
                </div>
              )}
            </dl>
          </div>
        </div>

        {/* Notes */}
        {vehicle.notes && (
          <div>
            <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
              <ClipboardDocumentListIcon className="w-4 h-4 mr-2" />
              Notes
            </h4>
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <p className="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                {vehicle.notes}
              </p>
            </div>
          </div>
        )}

        {/* Timestamps */}
        <div>
          <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
            <CalendarIcon className="w-4 h-4 mr-2" />
            Record Information
          </h4>
          <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
              <dd className="text-sm text-gray-900 dark:text-white">
                {formatDate(vehicle.created_at)}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
              <dd className="text-sm text-gray-900 dark:text-white">
                {formatDate(vehicle.updated_at)}
              </dd>
            </div>
          </dl>
        </div>

        {/* Stats (if available) */}
        {(vehicle.maintenance_logs_count !== undefined || vehicle.fuel_logs_count !== undefined) && (
          <div>
            <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
              <IdentificationIcon className="w-4 h-4 mr-2" />
              Activity Summary
            </h4>
            <dl className="grid grid-cols-2 gap-4">
              {vehicle.maintenance_logs_count !== undefined && (
                <div className="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                  <dt className="text-sm font-medium text-blue-700 dark:text-blue-300">Maintenance Records</dt>
                  <dd className="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                    {vehicle.maintenance_logs_count}
                  </dd>
                </div>
              )}
              {vehicle.fuel_logs_count !== undefined && (
                <div className="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                  <dt className="text-sm font-medium text-green-700 dark:text-green-300">Fuel Records</dt>
                  <dd className="text-2xl font-semibold text-green-900 dark:text-green-100">
                    {vehicle.fuel_logs_count}
                  </dd>
                </div>
              )}
            </dl>
          </div>
        )}
      </div>
    </Modal>
  );
};
