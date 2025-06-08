<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::with(['make', 'currentlyAssignedUser']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by make
        if ($request->has('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        // Filter by fuel type
        if ($request->has('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filter by year range
        if ($request->has('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }
        if ($request->has('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        // Filter by mileage range
        if ($request->has('mileage_from')) {
            $query->where('mileage', '>=', $request->mileage_from);
        }
        if ($request->has('mileage_to')) {
            $query->where('mileage', '<=', $request->mileage_to);
        }

        // Filter vehicles with expiring documents
        if ($request->boolean('expiring_documents')) {
            $query->expiringDocuments($request->get('expiring_days', 30));
        }

        // Search by multiple fields
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', '%' . $search . '%')
                  ->orWhere('license_plate', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%')
                  ->orWhereHas('make', function($makeQuery) use ($search) {
                      $makeQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'make_name') {
            $query->join('vehicle_makes', 'vehicles.make_id', '=', 'vehicle_makes.id')
                  ->orderBy('vehicle_makes.name', $sortOrder)
                  ->select('vehicles.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginate
        $perPage = $request->get('per_page', 15);
        $vehicles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $vehicles->items(),
            'pagination' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total()
            ]
        ]);
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make_id' => 'required|exists:vehicle_makes,id',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'vin' => 'required|string|size:17|unique:vehicles',
            'color' => 'nullable|string|max:50',
            'fuel_type' => ['required', Rule::in(Vehicle::getFuelTypes())],
            'engine_size' => 'nullable|numeric|min:0.5|max:10',
            'transmission' => ['required', Rule::in(Vehicle::getTransmissionTypes())],
            'mileage' => 'required|integer|min:0',
            'purchase_date' => 'required|date|before_or_equal:today',
            'purchase_price' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(Vehicle::getStatuses())],
            'insurance_expiry' => 'required|date|after:today',
            'registration_expiry' => 'required|date|after:today',
            'inspection_expiry' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle = Vehicle::create($request->all());
        $vehicle->load('make');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle
        ], 201);
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load([
            'make',
            'currentlyAssignedUser',
            'maintenanceLogs.maintenanceType',
            'fuelLogs' => function($query) {
                $query->orderBy('fuel_date', 'desc')->limit(10);
            },
            'documents'
        ]);

        // Add calculated fields
        $vehicle->latest_fuel_efficiency = $vehicle->getLatestFuelEfficiency();
        $vehicle->has_expiring_documents = $vehicle->hasExpiringDocuments();

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ]);
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make_id' => 'sometimes|required|exists:vehicle_makes,id',
            'model' => 'sometimes|required|string|max:100',
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'sometimes|required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'vin' => 'sometimes|required|string|size:17|unique:vehicles,vin,' . $vehicle->id,
            'color' => 'nullable|string|max:50',
            'fuel_type' => ['sometimes', 'required', Rule::in(Vehicle::getFuelTypes())],
            'engine_size' => 'nullable|numeric|min:0.5|max:10',
            'transmission' => ['sometimes', 'required', Rule::in(Vehicle::getTransmissionTypes())],
            'mileage' => 'sometimes|required|integer|min:0',
            'purchase_date' => 'sometimes|required|date|before_or_equal:today',
            'purchase_price' => 'sometimes|required|numeric|min:0',
            'status' => ['sometimes', 'required', Rule::in(Vehicle::getStatuses())],
            'insurance_expiry' => 'sometimes|required|date',
            'registration_expiry' => 'sometimes|required|date',
            'inspection_expiry' => 'sometimes|required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle->update($request->all());
        $vehicle->load('make');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle
        ]);
    }

    /**
     * Remove the specified vehicle (soft delete)
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        // Check if vehicle has active assignments
        if ($vehicle->currentlyAssignedUser()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vehicle with active assignments'
            ], 422);
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    /**
     * Get vehicle statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::active()->count(),
            'vehicles_in_maintenance' => Vehicle::byStatus(Vehicle::STATUS_MAINTENANCE)->count(),
            'inactive_vehicles' => Vehicle::byStatus(Vehicle::STATUS_INACTIVE)->count(),
            'vehicles_by_fuel_type' => [
                'gasoline' => Vehicle::byFuelType(Vehicle::FUEL_GASOLINE)->count(),
                'diesel' => Vehicle::byFuelType(Vehicle::FUEL_DIESEL)->count(),
                'hybrid' => Vehicle::byFuelType(Vehicle::FUEL_HYBRID)->count(),
                'electric' => Vehicle::byFuelType(Vehicle::FUEL_ELECTRIC)->count(),
            ],
            'vehicles_with_expiring_documents' => Vehicle::expiringDocuments(30)->count(),
            'average_vehicle_age' => Vehicle::selectRaw('AVG(YEAR(NOW()) - year) as avg_age')->value('avg_age'),
            'average_mileage' => Vehicle::avg('mileage'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get vehicles with expiring documents
     */
    public function expiringDocuments(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $vehicles = Vehicle::with('make')
                          ->expiringDocuments($days)
                          ->get()
                          ->map(function($vehicle) {
                              $expiringItems = [];
                              
                              if ($vehicle->insurance_expiry && $vehicle->insurance_expiry->diffInDays(Carbon::now()) <= 30) {
                                  $expiringItems[] = [
                                      'type' => 'insurance',
                                      'expiry_date' => $vehicle->insurance_expiry,
                                      'days_until_expiry' => $vehicle->insurance_expiry->diffInDays(Carbon::now())
                                  ];
                              }
                              
                              if ($vehicle->registration_expiry && $vehicle->registration_expiry->diffInDays(Carbon::now()) <= 30) {
                                  $expiringItems[] = [
                                      'type' => 'registration',
                                      'expiry_date' => $vehicle->registration_expiry,
                                      'days_until_expiry' => $vehicle->registration_expiry->diffInDays(Carbon::now())
                                  ];
                              }
                              
                              if ($vehicle->inspection_expiry && $vehicle->inspection_expiry->diffInDays(Carbon::now()) <= 30) {
                                  $expiringItems[] = [
                                      'type' => 'inspection',
                                      'expiry_date' => $vehicle->inspection_expiry,
                                      'days_until_expiry' => $vehicle->inspection_expiry->diffInDays(Carbon::now())
                                  ];
                              }
                              
                              $vehicle->expiring_items = $expiringItems;
                              return $vehicle;
                          });

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    /**
     * Update vehicle status
     */
    public function updateStatus(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(Vehicle::getStatuses())],
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle->update([
            'status' => $request->status,
            'notes' => $request->notes ? ($vehicle->notes . "\n" . $request->notes) : $vehicle->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle status updated successfully',
            'data' => $vehicle
        ]);
    }
}
