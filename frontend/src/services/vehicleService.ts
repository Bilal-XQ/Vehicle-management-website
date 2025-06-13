import axios from 'axios';
import { 
  Vehicle, 
  CreateVehicleRequest, 
  UpdateVehicleRequest, 
  VehicleFilters, 
  VehiclesResponse,
  VehicleStats,
  VehicleMake 
} from '../types/vehicle';

const API_BASE = 'http://localhost:8000/simple-api.php/api';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add request interceptor to include auth token
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Add response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
  mileage: number;
}

export interface VehicleUpdateData extends Partial<VehicleCreateData> {
  id: number;
}

export interface VehicleFilters {
  make?: string;
  model?: string;
  status?: string;
  year?: number;
  search?: string;
}

export interface PaginatedVehicles {
  data: Vehicle[];
  meta: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}

// Vehicle service class
class VehicleService {
  private endpoint = '/vehicles';

  async getAll(
    page: number = 1,
    filters: VehicleFilters = {}
  ): Promise<ApiResponse<PaginatedVehicles>> {
    const searchParams = new URLSearchParams({
      page: page.toString(),
      ...Object.fromEntries(
        Object.entries(filters).filter(([_, value]) => value !== undefined)
      ),
    });

    return apiService.get<PaginatedVehicles>(
      `${this.endpoint}?${searchParams.toString()}`
    );
  }

  async getById(id: number): Promise<ApiResponse<Vehicle>> {
    return apiService.get<Vehicle>(`${this.endpoint}/${id}`);
  }

  async create(data: VehicleCreateData): Promise<ApiResponse<Vehicle>> {
    return apiService.post<Vehicle>(this.endpoint, data);
  }

  async update(data: VehicleUpdateData): Promise<ApiResponse<Vehicle>> {
    const { id, ...updateData } = data;
    return apiService.put<Vehicle>(`${this.endpoint}/${id}`, updateData);
  }

  async delete(id: number): Promise<ApiResponse<void>> {
    return apiService.delete<void>(`${this.endpoint}/${id}`);
  }

  async getStatistics(): Promise<ApiResponse<{
    total: number;
    active: number;
    maintenance: number;
    retired: number;
  }>> {
    return apiService.get(`${this.endpoint}/statistics`);
  }
}

export const vehicleService = new VehicleService();
export default VehicleService;
