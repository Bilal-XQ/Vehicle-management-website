// Vehicle Management Types
export enum VehicleStatus {
  ACTIVE = 'active',
  MAINTENANCE = 'maintenance',
  RETIRED = 'retired'
}

export interface Vehicle {
  id: number;
  make: string;
  model: string;
  year: number;
  license_plate: string;
  status: VehicleStatus;
  mileage: number;
  color?: string;
  vin?: string;
  notes?: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  // Relations
  make_name?: string;
  maintenance_logs_count?: number;
  fuel_logs_count?: number;
}

export interface VehicleMake {
  id: number;
  name: string;
  country: string;
  created_at: string;
  updated_at: string;
}

export interface CreateVehicleRequest {
  make: string;
  model: string;
  year: number;
  license_plate: string;
  status: VehicleStatus;
  mileage: number;
  color?: string;
  vin?: string;
  notes?: string;
}

export interface UpdateVehicleRequest extends Partial<CreateVehicleRequest> {
  id: number;
}

export interface VehicleFilters {
  search?: string;
  make?: string;
  status?: VehicleStatus;
  year_from?: number;
  year_to?: number;
  page?: number;
  per_page?: number;
}

export interface VehiclesResponse {
  data: Vehicle[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  links: {
    first: string;
    last: string;
    prev?: string;
    next?: string;
  };
}

export interface VehicleStats {
  total: number;
  active: number;
  maintenance: number;
  retired: number;
  average_mileage: number;
  newest_vehicle: Vehicle | null;
  oldest_vehicle: Vehicle | null;
}
