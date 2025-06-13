import React, { useState, useEffect } from 'react';
import { Vehicle, VehicleStatus, VehicleMake, CreateVehicleRequest, UpdateVehicleRequest } from '../../types/vehicle';
import { vehicleService } from '../../services/vehicleApiService';
import { Input } from '../ui/Input';
import { Select } from '../ui/Select';
import { Textarea } from '../ui/Textarea';
import Button from '../ui/Button';
import { LoadingSpinner } from '../ui/LoadingSpinner';

interface VehicleFormProps {
  vehicle?: Vehicle;
  isEdit?: boolean;
  onSubmit: (data: CreateVehicleRequest | UpdateVehicleRequest) => Promise<void>;
  onCancel: () => void;
  isLoading?: boolean;
}

interface FormData {
  make: string;
  model: string;
  year: number | '';
  license_plate: string;
  status: VehicleStatus;
  mileage: number | '';
  color: string;
  vin: string;
  notes: string;
}

interface FormErrors {
  make?: string;
  model?: string;
  year?: string;
  license_plate?: string;
  status?: string;
  mileage?: string;
  color?: string;
  vin?: string;
  notes?: string;
}

export const VehicleForm: React.FC<VehicleFormProps> = ({
  vehicle,
  isEdit = false,
  onSubmit,
  onCancel,
  isLoading = false
}) => {
  const [formData, setFormData] = useState<FormData>({
    make: vehicle?.make || '',
    model: vehicle?.model || '',
    year: vehicle?.year || '',
    license_plate: vehicle?.license_plate || '',
    status: vehicle?.status || VehicleStatus.ACTIVE,
    mileage: vehicle?.mileage || '',
    color: vehicle?.color || '',
    vin: vehicle?.vin || '',
    notes: vehicle?.notes || ''
  });

  const [errors, setErrors] = useState<FormErrors>({});
  const [vehicleMakes, setVehicleMakes] = useState<VehicleMake[]>([]);
  const [loadingMakes, setLoadingMakes] = useState(true);

  // Load vehicle makes on component mount
  useEffect(() => {
    const loadVehicleMakes = async () => {
      try {
        const makes = await vehicleService.getVehicleMakes();
        setVehicleMakes(makes);
      } catch (error) {
        console.error('Failed to load vehicle makes:', error);
      } finally {
        setLoadingMakes(false);
      }
    };

    loadVehicleMakes();
  }, []);

  const handleInputChange = (field: keyof FormData, value: string | number) => {
    setFormData(prev => ({ ...prev, [field]: value }));
    // Clear error when user starts typing
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: undefined }));
    }
  };

  const validateForm = (): boolean => {
    const newErrors: FormErrors = {};

    if (!formData.make.trim()) {
      newErrors.make = 'Make is required';
    }

    if (!formData.model.trim()) {
      newErrors.model = 'Model is required';
    }

    if (!formData.year || formData.year < 1900 || formData.year > new Date().getFullYear() + 1) {
      newErrors.year = `Year must be between 1900 and ${new Date().getFullYear() + 1}`;
    }

    if (!formData.license_plate.trim()) {
      newErrors.license_plate = 'License plate is required';
    } else if (formData.license_plate.length < 2) {
      newErrors.license_plate = 'License plate must be at least 2 characters';
    }

    if (!formData.status) {
      newErrors.status = 'Status is required';
    }

    if (formData.mileage !== '' && (formData.mileage < 0 || formData.mileage > 999999)) {
      newErrors.mileage = 'Mileage must be between 0 and 999,999';
    }

    if (formData.vin && formData.vin.length !== 17) {
      newErrors.vin = 'VIN must be exactly 17 characters';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    const submitData = {
      ...formData,
      year: Number(formData.year),
      mileage: formData.mileage ? Number(formData.mileage) : 0
    };

    try {
      await onSubmit(submitData);
    } catch (error) {
      console.error('Form submission error:', error);
    }
  };

  const statusOptions = [
    { value: VehicleStatus.ACTIVE, label: 'Active' },
    { value: VehicleStatus.MAINTENANCE, label: 'Maintenance' },
    { value: VehicleStatus.RETIRED, label: 'Retired' }
  ];

  const makeOptions = vehicleMakes.map(make => ({
    value: make.name,
    label: `${make.name} (${make.country})`
  }));

  if (loadingMakes) {
    return (
      <div className="flex justify-center py-8">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Make */}
        <Select
          label="Make"
          value={formData.make}
          onChange={(e) => handleInputChange('make', e.target.value)}
          options={makeOptions}
          placeholder="Select vehicle make"
          error={errors.make}
          required
        />

        {/* Model */}
        <Input
          label="Model"
          value={formData.model}
          onChange={(e) => handleInputChange('model', e.target.value)}
          placeholder="e.g., Camry, Civic, F-150"
          error={errors.model}
          required
        />

        {/* Year */}
        <Input
          label="Year"
          type="number"
          value={formData.year}
          onChange={(e) => handleInputChange('year', parseInt(e.target.value) || '')}
          placeholder="e.g., 2022"
          min="1900"
          max={new Date().getFullYear() + 1}
          error={errors.year}
          required
        />

        {/* License Plate */}
        <Input
          label="License Plate"
          value={formData.license_plate}
          onChange={(e) => handleInputChange('license_plate', e.target.value.toUpperCase())}
          placeholder="e.g., ABC-123"
          error={errors.license_plate}
          required
        />

        {/* Status */}
        <Select
          label="Status"
          value={formData.status}
          onChange={(e) => handleInputChange('status', e.target.value as VehicleStatus)}
          options={statusOptions}
          error={errors.status}
          required
        />

        {/* Mileage */}
        <Input
          label="Mileage"
          type="number"
          value={formData.mileage}
          onChange={(e) => handleInputChange('mileage', parseInt(e.target.value) || '')}
          placeholder="e.g., 15000"
          min="0"
          max="999999"
          error={errors.mileage}
          helperText="Enter current mileage (optional)"
        />

        {/* Color */}
        <Input
          label="Color"
          value={formData.color}
          onChange={(e) => handleInputChange('color', e.target.value)}
          placeholder="e.g., White, Blue, Red"
          error={errors.color}
        />

        {/* VIN */}
        <Input
          label="VIN"
          value={formData.vin}
          onChange={(e) => handleInputChange('vin', e.target.value.toUpperCase())}
          placeholder="17-character VIN"
          maxLength={17}
          error={errors.vin}
          helperText="Vehicle Identification Number (optional)"
        />
      </div>

      {/* Notes */}
      <Textarea
        label="Notes"
        value={formData.notes}
        onChange={(e) => handleInputChange('notes', e.target.value)}
        placeholder="Additional notes about this vehicle..."
        rows={3}
        error={errors.notes}
      />

      {/* Form Actions */}
      <div className="flex justify-end space-x-3 pt-6 border-t dark:border-gray-600">
        <Button
          type="button"
          variant="outline"
          onClick={onCancel}
          disabled={isLoading}
        >
          Cancel
        </Button>
        <Button
          type="submit"
          disabled={isLoading}
          className="min-w-[120px]"
        >
          {isLoading ? (
            <LoadingSpinner size="sm" className="text-white" />
          ) : (
            isEdit ? 'Update Vehicle' : 'Add Vehicle'
          )}
        </Button>
      </div>
    </form>
  );
};
