<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleMake;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class VehicleMakeController extends Controller
{
    /**
     * Display a listing of vehicle makes.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VehicleMake::query();

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('country', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'country', 'created_at', 'vehicles_count'])) {
            if ($sortBy === 'vehicles_count') {
                $query->withCount('vehicles')->orderBy('vehicles_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Include vehicle count if requested
        if ($request->boolean('include_vehicle_count')) {
            $query->withCount('vehicles');
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $vehicleMakes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $vehicleMakes->items(),
            'pagination' => [
                'current_page' => $vehicleMakes->currentPage(),
                'last_page' => $vehicleMakes->lastPage(),
                'per_page' => $vehicleMakes->perPage(),
                'total' => $vehicleMakes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created vehicle make.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:vehicle_makes,name',
            'country' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        $vehicleMake = VehicleMake::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle make created successfully',
            'data' => $vehicleMake
        ], 201);
    }

    /**
     * Display the specified vehicle make.
     */
    public function show(Request $request, VehicleMake $vehicleMake): JsonResponse
    {
        // Include vehicles if requested
        if ($request->boolean('include_vehicles')) {
            $vehicleMake->load(['vehicles' => function ($query) {
                $query->select('id', 'vehicle_make_id', 'model', 'year', 'license_plate', 'status')
                      ->where('status', '!=', 'scrapped');
            }]);
        }

        // Include vehicle count
        $vehicleMake->loadCount('vehicles');

        return response()->json([
            'success' => true,
            'data' => $vehicleMake
        ]);
    }

    /**
     * Update the specified vehicle make.
     */
    public function update(Request $request, VehicleMake $vehicleMake): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehicle_makes', 'name')->ignore($vehicleMake->id)
            ],
            'country' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        $vehicleMake->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle make updated successfully',
            'data' => $vehicleMake->fresh()
        ]);
    }

    /**
     * Remove the specified vehicle make.
     */
    public function destroy(VehicleMake $vehicleMake): JsonResponse
    {
        // Check if there are vehicles using this make
        if ($vehicleMake->vehicles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vehicle make that is in use by vehicles'
            ], 422);
        }

        $vehicleMake->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle make deleted successfully'
        ]);
    }

    /**
     * Get vehicle makes for dropdown/select options.
     */
    public function options(): JsonResponse
    {
        $vehicleMakes = VehicleMake::active()
            ->select('id', 'name', 'country')
            ->orderBy('name')
            ->get()
            ->map(function ($make) {
                return [
                    'value' => $make->id,
                    'label' => $make->name,
                    'country' => $make->country
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vehicleMakes
        ]);
    }

    /**
     * Toggle active status of vehicle make.
     */
    public function toggleStatus(VehicleMake $vehicleMake): JsonResponse
    {
        $vehicleMake->update(['is_active' => !$vehicleMake->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle make status updated successfully',
            'data' => [
                'id' => $vehicleMake->id,
                'is_active' => $vehicleMake->is_active
            ]
        ]);
    }

    /**
     * Get statistics for vehicle makes.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_makes' => VehicleMake::count(),
            'active_makes' => VehicleMake::active()->count(),
            'inactive_makes' => VehicleMake::inactive()->count(),
            'top_makes' => VehicleMake::withCount('vehicles')
                ->orderBy('vehicles_count', 'desc')
                ->take(5)
                ->get(['id', 'name', 'vehicles_count']),
            'countries' => VehicleMake::selectRaw('country, COUNT(*) as count')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
