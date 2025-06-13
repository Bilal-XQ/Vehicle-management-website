import React, { useState } from 'react';
import { Vehicle, CreateVehicleRequest, UpdateVehicleRequest } from '../../types/vehicle';
import { vehicleService } from '../../services/vehicleApiService';
import Modal from '../ui/Modal';
import { VehicleForm } from '../forms/VehicleForm';

interface VehicleFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  vehicle?: Vehicle | null;
  onSuccess: (vehicle: Vehicle) => void;
  onError?: (error: string) => void;
}

export const VehicleFormModal: React.FC<VehicleFormModalProps> = ({
  isOpen,
  onClose,
  vehicle,
  onSuccess,
  onError
}) => {
  const [isLoading, setIsLoading] = useState(false);
  const isEdit = !!vehicle;

  const handleSubmit = async (data: CreateVehicleRequest | UpdateVehicleRequest) => {
    setIsLoading(true);
    
    try {
      let result: Vehicle;
      
      if (isEdit && vehicle) {
        result = await vehicleService.updateVehicle(vehicle.id, data as UpdateVehicleRequest);
      } else {
        result = await vehicleService.createVehicle(data as CreateVehicleRequest);
      }
      
      onSuccess(result);
      onClose();
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 
                          error.message || 
                          `Failed to ${isEdit ? 'update' : 'create'} vehicle`;
      
      if (onError) {
        onError(errorMessage);
      } else {
        console.error('Vehicle form error:', errorMessage);
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleClose = () => {
    if (!isLoading) {
      onClose();
    }
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={handleClose}
      title={isEdit ? 'Edit Vehicle' : 'Add New Vehicle'}
      size="xl"
      showCloseButton={!isLoading}
    >      <VehicleForm
        vehicle={vehicle || undefined}
        isEdit={isEdit}
        onSubmit={handleSubmit}
        onCancel={handleClose}
        isLoading={isLoading}
      />
    </Modal>
  );
};
