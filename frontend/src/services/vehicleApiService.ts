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

export const vehicleService = {
  // Get all vehicles with pagination and filters
  async getVehicles(filters: VehicleFilters = {}): Promise<VehiclesResponse> {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        params.append(key, value.toString());
      }
    });
    
    const response = await apiClient.get(`/vehicles?${params}`);
    return response.data;
  },

  // Get single vehicle
  async getVehicle(id: number): Promise<Vehicle> {
    const response = await apiClient.get(`/vehicles/${id}`);
    return response.data.data;
  },

  // Create new vehicle
  async createVehicle(data: CreateVehicleRequest): Promise<Vehicle> {
    const response = await apiClient.post('/vehicles', data);
    return response.data.data;
  },

  // Update vehicle
  async updateVehicle(id: number, data: UpdateVehicleRequest): Promise<Vehicle> {
    const response = await apiClient.put(`/vehicles/${id}`, data);
    return response.data.data;
  },

  // Delete vehicle (soft delete)
  async deleteVehicle(id: number): Promise<void> {
    await apiClient.delete(`/vehicles/${id}`);
  },

  // Get vehicle statistics
  async getVehicleStats(): Promise<VehicleStats> {
    const response = await apiClient.get('/vehicles/stats');
    return response.data.data;
  },

  // Get vehicle makes
  async getVehicleMakes(): Promise<VehicleMake[]> {
    const response = await apiClient.get('/vehicle-makes');
    return response.data.data;
  },

  // Search vehicles
  async searchVehicles(query: string): Promise<Vehicle[]> {
    const response = await apiClient.get(`/vehicles/search?q=${encodeURIComponent(query)}`);
    return response.data.data;
  }
};
