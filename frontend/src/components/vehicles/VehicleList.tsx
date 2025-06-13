import React, { useState, useEffect } from 'react';
import { Vehicle, VehicleFilters } from '../../types/vehicle';
import { vehicleService } from '../../services/vehicleApiService';
import { VehicleCard } from './VehicleCard';
import { VehicleFiltersComponent } from './VehicleFilters';
import { VehicleFormModal } from './VehicleFormModal';
import { VehicleDetailsModal } from './VehicleDetailsModal';
import { Pagination } from '../ui/Pagination';
import { LoadingSpinner } from '../ui/LoadingSpinner';
import { useToast } from '../ui/Toast';
import { PlusIcon } from '@heroicons/react/24/outline';
import Button from '../ui/Button';

interface VehicleListProps {
  className?: string;
}

export const VehicleList: React.FC<VehicleListProps> = () => {
  const [vehicles, setVehicles] = useState<Vehicle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const { showSuccess, showError } = useToast();
  
  // Modal states
  const [showAddModal, setShowAddModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [showDetailsModal, setShowDetailsModal] = useState(false);
  const [selectedVehicle, setSelectedVehicle] = useState<Vehicle | null>(null);
  
  // Filter and pagination states
  const [filters, setFilters] = useState<VehicleFilters>({ 
    page: 1, 
    per_page: 12 
  });
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0,
    from: 0,
    to: 0
  });  const loadVehicles = async () => {
    try {
      setLoading(true);
      setError(null);
      
      // Use the real API now
      const response = await vehicleService.getVehicles(filters);
      setVehicles(response.data);
      setPagination(response.meta);
    } catch (err) {
      setError('Failed to load vehicles');
      console.error('Error loading vehicles:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadVehicles();
  }, [filters]);

  const handleFilterChange = (newFilters: VehicleFilters) => {
    setFilters(prev => ({
      ...prev,
      ...newFilters,
      page: newFilters.page || 1
    }));
  };

  const handleClearFilters = () => {
    setFilters({ page: 1, per_page: 12 });
  };

  const handlePageChange = (page: number) => {
    setFilters(prev => ({ ...prev, page }));
  };

  // Modal handlers
  const handleAddVehicle = () => {
    setSelectedVehicle(null);
    setShowAddModal(true);
  };

  const handleEditVehicle = (vehicle: Vehicle) => {
    setSelectedVehicle(vehicle);
    setShowEditModal(true);
  };

  const handleViewVehicle = (vehicle: Vehicle) => {
    setSelectedVehicle(vehicle);
    setShowDetailsModal(true);
  };
  const handleDeleteVehicle = async (id: number) => {
    if (confirm('Are you sure you want to delete this vehicle?')) {
      try {
        await vehicleService.deleteVehicle(id);
        showSuccess('Vehicle deleted', 'Vehicle has been successfully removed from the fleet.');
        loadVehicles(); // Reload the list
      } catch (error) {
        console.error('Failed to delete vehicle:', error);
        showError('Delete failed', 'Failed to delete vehicle. Please try again.');
      }
    }
  };

  const handleVehicleSuccess = (vehicle: Vehicle) => {
    const isEdit = selectedVehicle !== null;
    showSuccess(
      isEdit ? 'Vehicle updated' : 'Vehicle added',
      `${vehicle.year} ${vehicle.make} ${vehicle.model} has been successfully ${isEdit ? 'updated' : 'added'}.`
    );
    loadVehicles(); // Reload the list to show the new/updated vehicle
  };

  const handleVehicleError = (errorMessage: string) => {
    showError('Operation failed', errorMessage);
  };

  if (error) {
    return (
      <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
        <p className="text-red-800 dark:text-red-200">{error}</p>
        <Button 
          onClick={loadVehicles} 
          className="mt-2"
          variant="outline"
        >
          Try Again
        </Button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Vehicle Fleet
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Manage your vehicle inventory
          </p>
        </div>        <Button onClick={handleAddVehicle} className="flex items-center">
          <PlusIcon className="w-4 h-4 mr-2" />
          Add Vehicle
        </Button>
      </div>

      {/* Filters */}
      <VehicleFiltersComponent
        filters={filters}
        onFiltersChange={handleFilterChange}
        onClearFilters={handleClearFilters}
      />

      {/* Results Summary */}
      <div className="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>
          Showing {pagination.from}-{pagination.to} of {pagination.total} vehicles
        </span>
        <span>
          Page {pagination.current_page} of {pagination.last_page}
        </span>
      </div>

      {/* Loading State */}
      {loading && (
        <div className="flex justify-center py-12">
          <LoadingSpinner size="lg" />
        </div>
      )}

      {/* Vehicle Grid */}
      {!loading && vehicles.length > 0 && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {vehicles.map((vehicle) => (            <VehicleCard
              key={vehicle.id}
              vehicle={vehicle}
              onEdit={handleEditVehicle}
              onView={handleViewVehicle}
              onDelete={handleDeleteVehicle}
            />
          ))}
        </div>
      )}

      {/* Empty State */}
      {!loading && vehicles.length === 0 && (
        <div className="text-center py-12">
          <div className="mx-auto h-24 w-24 text-gray-400">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"/>
            </svg>
          </div>
          <h3 className="mt-4 text-lg font-medium text-gray-900 dark:text-white">
            No vehicles found
          </h3>
          <p className="mt-2 text-gray-500 dark:text-gray-400">
            Get started by adding your first vehicle to the fleet.
          </p>          <Button onClick={handleAddVehicle} className="mt-4">
            <PlusIcon className="w-4 h-4 mr-2" />
            Add Your First Vehicle
          </Button>
        </div>
      )}

      {/* Pagination */}
      {!loading && vehicles.length > 0 && pagination.last_page > 1 && (
        <Pagination
          currentPage={pagination.current_page}
          totalPages={pagination.last_page}
          onPageChange={handlePageChange}
        />
      )}

      {/* Modals */}
      <VehicleFormModal
        isOpen={showAddModal}
        onClose={() => setShowAddModal(false)}
        onSuccess={handleVehicleSuccess}
        onError={handleVehicleError}
      />

      <VehicleFormModal
        isOpen={showEditModal}
        onClose={() => setShowEditModal(false)}
        vehicle={selectedVehicle}
        onSuccess={handleVehicleSuccess}
        onError={handleVehicleError}
      />

      <VehicleDetailsModal
        isOpen={showDetailsModal}
        onClose={() => setShowDetailsModal(false)}
        vehicle={selectedVehicle}
        onEdit={handleEditVehicle}
        onDelete={handleDeleteVehicle}
      />
    </div>
  );
};
