<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MaintenanceTypeController extends Controller
{
    /**
     * Display a listing of maintenance types.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MaintenanceType::query();

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by frequency range
        if ($request->has('min_frequency')) {
            $query->where('frequency_km', '>=', $request->min_frequency);
        }
        if ($request->has('max_frequency')) {
            $query->where('frequency_km', '<=', $request->max_frequency);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'category', 'frequency_km', 'estimated_cost', 'created_at', 'maintenance_logs_count'])) {
            if ($sortBy === 'maintenance_logs_count') {
                $query->withCount('maintenanceLogs')->orderBy('maintenance_logs_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Include maintenance count if requested
        if ($request->boolean('include_maintenance_count')) {
            $query->withCount('maintenanceLogs');
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $maintenanceTypes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $maintenanceTypes->items(),
            'pagination' => [
                'current_page' => $maintenanceTypes->currentPage(),
                'last_page' => $maintenanceTypes->lastPage(),
                'per_page' => $maintenanceTypes->perPage(),
                'total' => $maintenanceTypes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created maintenance type.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:maintenance_types,name',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:preventive,corrective,predictive,emergency',
            'frequency_km' => 'nullable|integer|min:1|max:200000',
            'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean'
        ]);

        $maintenanceType = MaintenanceType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance type created successfully',
            'data' => $maintenanceType
        ], 201);
    }

    /**
     * Display the specified maintenance type.
     */
    public function show(Request $request, MaintenanceType $maintenanceType): JsonResponse
    {
        // Include recent maintenance logs if requested
        if ($request->boolean('include_recent_logs')) {
            $maintenanceType->load(['maintenanceLogs' => function ($query) {
                $query->with(['vehicle:id,license_plate,model', 'user:id,name'])
                      ->latest()
                      ->take(10);
            }]);
        }

        // Include maintenance count and statistics
        $maintenanceType->loadCount('maintenanceLogs');
        
        if ($request->boolean('include_statistics')) {
            $maintenanceType->loadSum('maintenanceLogs', 'cost');
            $maintenanceType->loadAvg('maintenanceLogs', 'cost');
        }

        return response()->json([
            'success' => true,
            'data' => $maintenanceType
        ]);
    }

    /**
     * Update the specified maintenance type.
     */
    public function update(Request $request, MaintenanceType $maintenanceType): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('maintenance_types', 'name')->ignore($maintenanceType->id)
            ],
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:preventive,corrective,predictive,emergency',
            'frequency_km' => 'nullable|integer|min:1|max:200000',
            'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean'
        ]);

        $maintenanceType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance type updated successfully',
            'data' => $maintenanceType->fresh()
        ]);
    }

    /**
     * Remove the specified maintenance type.
     */
    public function destroy(MaintenanceType $maintenanceType): JsonResponse
    {
        // Check if there are maintenance logs using this type
        if ($maintenanceType->maintenanceLogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete maintenance type that is in use by maintenance logs'
            ], 422);
        }

        $maintenanceType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance type deleted successfully'
        ]);
    }

    /**
     * Get maintenance types for dropdown/select options.
     */
    public function options(Request $request): JsonResponse
    {
        $query = MaintenanceType::active()
            ->select('id', 'name', 'category', 'frequency_km', 'estimated_cost')
            ->orderBy('category')
            ->orderBy('name');

        // Filter by category if specified
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $maintenanceTypes = $query->get()
            ->groupBy('category')
            ->map(function ($types, $category) {
                return [
                    'category' => $category,
                    'types' => $types->map(function ($type) {
                        return [
                            'value' => $type->id,
                            'label' => $type->name,
                            'frequency_km' => $type->frequency_km,
                            'estimated_cost' => $type->estimated_cost
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $maintenanceTypes->values()
        ]);
    }

    /**
     * Toggle active status of maintenance type.
     */
    public function toggleStatus(MaintenanceType $maintenanceType): JsonResponse
    {
        $maintenanceType->update(['is_active' => !$maintenanceType->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance type status updated successfully',
            'data' => [
                'id' => $maintenanceType->id,
                'is_active' => $maintenanceType->is_active
            ]
        ]);
    }

    /**
     * Get statistics for maintenance types.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_types' => MaintenanceType::count(),
            'active_types' => MaintenanceType::active()->count(),
            'inactive_types' => MaintenanceType::inactive()->count(),
            'by_category' => MaintenanceType::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get(),
            'most_used' => MaintenanceType::withCount('maintenanceLogs')
                ->orderBy('maintenance_logs_count', 'desc')
                ->take(5)
                ->get(['id', 'name', 'category', 'maintenance_logs_count']),
            'cost_analysis' => [
                'average_estimated_cost' => MaintenanceType::whereNotNull('estimated_cost')
                    ->avg('estimated_cost'),
                'highest_cost_type' => MaintenanceType::whereNotNull('estimated_cost')
                    ->orderBy('estimated_cost', 'desc')
                    ->first(['name', 'estimated_cost']),
                'lowest_cost_type' => MaintenanceType::whereNotNull('estimated_cost')
                    ->orderBy('estimated_cost', 'asc')
                    ->first(['name', 'estimated_cost'])
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get maintenance types due for specific vehicle.
     */
    public function dueForVehicle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'current_mileage' => 'required|integer|min:0'
        ]);

        // This would require more complex logic to determine which maintenance is due
        // based on the vehicle's maintenance history and current mileage
        $maintenanceTypes = MaintenanceType::active()
            ->whereNotNull('frequency_km')
            ->get()
            ->filter(function ($type) use ($validated) {
                // Simplified logic - in real implementation, you'd check last maintenance date
                // and calculate if maintenance is due based on frequency_km
                return true; // Placeholder
            });

        return response()->json([
            'success' => true,
            'data' => $maintenanceTypes->values()
        ]);
    }
}
