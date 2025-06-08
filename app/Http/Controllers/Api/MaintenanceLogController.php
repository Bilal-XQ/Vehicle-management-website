<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MaintenanceLogController extends Controller
{
    /**
     * Display a listing of maintenance logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = MaintenanceLog::with(['vehicle.make', 'maintenanceType', 'performedBy']);

        // Filter by vehicle
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by maintenance type
        if ($request->has('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->maintenance_type_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('maintenance_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('maintenance_date', '<=', $request->date_to);
        }

        // Filter by cost range
        if ($request->has('cost_min')) {
            $query->where('cost', '>=', $request->cost_min);
        }
        if ($request->has('cost_max')) {
            $query->where('cost', '<=', $request->cost_max);
        }

        // Filter due for service
        if ($request->boolean('due_for_service')) {
            $query->dueForService($request->get('due_days', 30));
        }

        // Filter overdue
        if ($request->boolean('overdue')) {
            $query->scheduled()->whereNotNull('next_service_date')
                  ->where('next_service_date', '<', Carbon::now());
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('service_provider', 'like', '%' . $search . '%')
                  ->orWhere('receipt_number', 'like', '%' . $search . '%')
                  ->orWhereHas('vehicle', function($vehicleQuery) use ($search) {
                      $vehicleQuery->where('license_plate', 'like', '%' . $search . '%')
                                   ->orWhere('model', 'like', '%' . $search . '%');
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'maintenance_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $maintenanceLogs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $maintenanceLogs->items(),
            'pagination' => [
                'current_page' => $maintenanceLogs->currentPage(),
                'last_page' => $maintenanceLogs->lastPage(),
                'per_page' => $maintenanceLogs->perPage(),
                'total' => $maintenanceLogs->total()
            ]
        ]);
    }

    /**
     * Store a newly created maintenance log
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'performed_by' => 'required|exists:users,id',
            'service_provider' => 'nullable|string|max:255',
            'maintenance_date' => 'required|date',
            'mileage_at_service' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'parts_used' => 'nullable|array',
            'parts_used.*.name' => 'required_with:parts_used|string|max:255',
            'parts_used.*.part_number' => 'nullable|string|max:100',
            'parts_used.*.cost' => 'nullable|numeric|min:0',
            'labor_hours' => 'nullable|numeric|min:0|max:24',
            'description' => 'required|string|max:1000',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date|after:maintenance_date',
            'warranty_until' => 'nullable|date|after:maintenance_date',
            'receipt_number' => 'nullable|string|max:100',
            'status' => ['required', Rule::in(MaintenanceLog::getStatuses())],
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $maintenanceLog = MaintenanceLog::create($request->all());
        $maintenanceLog->load(['vehicle.make', 'maintenanceType', 'performedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance log created successfully',
            'data' => $maintenanceLog
        ], 201);
    }

    /**
     * Display the specified maintenance log
     */
    public function show(MaintenanceLog $maintenanceLog): JsonResponse
    {
        $maintenanceLog->load(['vehicle.make', 'maintenanceType', 'performedBy']);

        // Add calculated fields
        $maintenanceLog->total_parts_cost = $maintenanceLog->total_parts_cost;
        $maintenanceLog->labor_cost = $maintenanceLog->labor_cost;
        $maintenanceLog->warranty_status = $maintenanceLog->warranty_status;

        return response()->json([
            'success' => true,
            'data' => $maintenanceLog
        ]);
    }

    /**
     * Update the specified maintenance log
     */
    public function update(Request $request, MaintenanceLog $maintenanceLog): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'maintenance_type_id' => 'sometimes|required|exists:maintenance_types,id',
            'performed_by' => 'sometimes|required|exists:users,id',
            'service_provider' => 'nullable|string|max:255',
            'maintenance_date' => 'sometimes|required|date',
            'mileage_at_service' => 'sometimes|required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'parts_used' => 'nullable|array',
            'parts_used.*.name' => 'required_with:parts_used|string|max:255',
            'parts_used.*.part_number' => 'nullable|string|max:100',
            'parts_used.*.cost' => 'nullable|numeric|min:0',
            'labor_hours' => 'nullable|numeric|min:0|max:24',
            'description' => 'sometimes|required|string|max:1000',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date',
            'warranty_until' => 'nullable|date',
            'receipt_number' => 'nullable|string|max:100',
            'status' => ['sometimes', 'required', Rule::in(MaintenanceLog::getStatuses())],
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $maintenanceLog->update($request->all());
        $maintenanceLog->load(['vehicle.make', 'maintenanceType', 'performedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance log updated successfully',
            'data' => $maintenanceLog
        ]);
    }

    /**
     * Remove the specified maintenance log
     */
    public function destroy(MaintenanceLog $maintenanceLog): JsonResponse
    {
        $maintenanceLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance log deleted successfully'
        ]);
    }

    /**
     * Get maintenance statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_maintenance_logs' => MaintenanceLog::count(),
            'completed_maintenance' => MaintenanceLog::completed()->count(),
            'scheduled_maintenance' => MaintenanceLog::scheduled()->count(),
            'overdue_maintenance' => MaintenanceLog::scheduled()
                                                  ->whereNotNull('next_service_date')
                                                  ->where('next_service_date', '<', Carbon::now())
                                                  ->count(),
            'total_maintenance_cost' => MaintenanceLog::completed()->sum('cost'),
            'average_maintenance_cost' => MaintenanceLog::completed()->avg('cost'),
            'maintenance_this_month' => MaintenanceLog::whereBetween('maintenance_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
            'maintenance_by_type' => MaintenanceLog::with('maintenanceType')
                                                  ->get()
                                                  ->groupBy('maintenanceType.category')
                                                  ->map(function($group) {
                                                      return $group->count();
                                                  }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get upcoming maintenance
     */
    public function upcoming(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $upcomingMaintenance = MaintenanceLog::with(['vehicle.make', 'maintenanceType'])
                                            ->dueForService($days)
                                            ->orderBy('next_service_date')
                                            ->get();

        return response()->json([
            'success' => true,
            'data' => $upcomingMaintenance
        ]);
    }

    /**
     * Get overdue maintenance
     */
    public function overdue(): JsonResponse
    {
        $overdueMaintenance = MaintenanceLog::with(['vehicle.make', 'maintenanceType'])
                                           ->scheduled()
                                           ->whereNotNull('next_service_date')
                                           ->where('next_service_date', '<', Carbon::now())
                                           ->orderBy('next_service_date')
                                           ->get();

        return response()->json([
            'success' => true,
            'data' => $overdueMaintenance
        ]);
    }

    /**
     * Get maintenance history for a vehicle
     */
    public function vehicleHistory(Vehicle $vehicle): JsonResponse
    {
        $maintenanceHistory = $vehicle->maintenanceLogs()
                                     ->with(['maintenanceType', 'performedBy'])
                                     ->orderBy('maintenance_date', 'desc')
                                     ->get();

        $stats = [
            'total_maintenance_records' => $maintenanceHistory->count(),
            'total_cost' => $maintenanceHistory->where('status', MaintenanceLog::STATUS_COMPLETED)->sum('cost'),
            'last_maintenance_date' => $maintenanceHistory->where('status', MaintenanceLog::STATUS_COMPLETED)->first()?->maintenance_date,
            'next_scheduled_maintenance' => $maintenanceHistory->where('status', MaintenanceLog::STATUS_SCHEDULED)->first()?->next_service_date,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'maintenance_history' => $maintenanceHistory,
                'statistics' => $stats
            ]
        ]);
    }

    /**
     * Update maintenance status
     */
    public function updateStatus(Request $request, MaintenanceLog $maintenanceLog): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(MaintenanceLog::getStatuses())],
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['status' => $request->status];

        if ($request->filled('notes')) {
            $updateData['notes'] = $maintenanceLog->notes ? 
                $maintenanceLog->notes . "\n" . $request->notes : 
                $request->notes;
        }

        // If completing maintenance, ensure we have required fields
        if ($request->status === MaintenanceLog::STATUS_COMPLETED) {
            if (!$maintenanceLog->cost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cost is required when completing maintenance'
                ], 422);
            }
        }

        $maintenanceLog->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance status updated successfully',
            'data' => $maintenanceLog
        ]);
    }
}
