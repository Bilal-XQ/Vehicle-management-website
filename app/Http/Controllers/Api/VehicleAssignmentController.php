<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleAssignment;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class VehicleAssignmentController extends Controller
{
    /**
     * Display a listing of vehicle assignments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VehicleAssignment::with(['vehicle:id,license_plate,model,year', 'user:id,name,email']);

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('vehicle', function ($q) use ($searchTerm) {
                $q->where('license_plate', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            })->orWhereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by vehicle
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'expired':
                    $query->expired();
                    break;
            }
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('assigned_at', '>=', Carbon::parse($request->start_date));
        }
        if ($request->has('end_date')) {
            $endDate = $request->end_date;
            $query->where(function ($q) use ($endDate) {
                $q->whereNull('unassigned_at')
                  ->orWhere('unassigned_at', '<=', Carbon::parse($endDate));
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'assigned_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['assigned_at', 'unassigned_at', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $assignments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $assignments->items(),
            'pagination' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'per_page' => $assignments->perPage(),
                'total' => $assignments->total(),
            ]
        ]);
    }

    /**
     * Store a newly created vehicle assignment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Check if vehicle is already assigned to someone else
        $existingAssignment = VehicleAssignment::where('vehicle_id', $validated['vehicle_id'])
            ->active()
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle is already assigned to another user',
                'data' => [
                    'current_assignment' => $existingAssignment->load(['user:id,name,email'])
                ]
            ], 422);
        }

        // Check if user already has an active assignment
        $userActiveAssignment = VehicleAssignment::where('user_id', $validated['user_id'])
            ->active()
            ->first();

        if ($userActiveAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'User already has an active vehicle assignment',
                'data' => [
                    'current_assignment' => $userActiveAssignment->load(['vehicle:id,license_plate,model'])
                ]
            ], 422);
        }

        $assignment = VehicleAssignment::create($validated);
        $assignment->load(['vehicle:id,license_plate,model,year', 'user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle assignment created successfully',
            'data' => $assignment
        ], 201);
    }

    /**
     * Display the specified vehicle assignment.
     */
    public function show(VehicleAssignment $vehicleAssignment): JsonResponse
    {
        $vehicleAssignment->load([
            'vehicle:id,license_plate,model,year,vin,status',
            'user:id,name,email,phone'
        ]);

        return response()->json([
            'success' => true,
            'data' => $vehicleAssignment
        ]);
    }

    /**
     * Update the specified vehicle assignment.
     */
    public function update(Request $request, VehicleAssignment $vehicleAssignment): JsonResponse
    {
        // Only allow updating notes and assigned_at for active assignments
        if (!$vehicleAssignment->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update inactive assignment'
            ], 422);
        }

        $validated = $request->validate([
            'assigned_at' => 'sometimes|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $vehicleAssignment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle assignment updated successfully',
            'data' => $vehicleAssignment->fresh(['vehicle:id,license_plate,model', 'user:id,name,email'])
        ]);
    }

    /**
     * Remove the specified vehicle assignment (unassign).
     */
    public function destroy(Request $request, VehicleAssignment $vehicleAssignment): JsonResponse
    {
        if (!$vehicleAssignment->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment is already inactive'
            ], 422);
        }

        $validated = $request->validate([
            'unassigned_at' => 'sometimes|date',
            'unassignment_reason' => 'nullable|string|max:500'
        ]);

        $vehicleAssignment->update([
            'unassigned_at' => $validated['unassigned_at'] ?? now(),
            'unassignment_reason' => $validated['unassignment_reason'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle unassigned successfully',
            'data' => $vehicleAssignment->fresh()
        ]);
    }

    /**
     * Get assignment history for a specific vehicle.
     */
    public function vehicleHistory(Vehicle $vehicle): JsonResponse
    {
        $assignments = VehicleAssignment::where('vehicle_id', $vehicle->id)
            ->with(['user:id,name,email'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => $vehicle->only(['id', 'license_plate', 'model', 'year']),
                'assignments' => $assignments
            ]
        ]);
    }

    /**
     * Get assignment history for a specific user.
     */
    public function userHistory(User $user): JsonResponse
    {
        $assignments = VehicleAssignment::where('user_id', $user->id)
            ->with(['vehicle:id,license_plate,model,year'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'name', 'email']),
                'assignments' => $assignments
            ]
        ]);
    }

    /**
     * Get current active assignments.
     */
    public function active(): JsonResponse
    {
        $assignments = VehicleAssignment::active()
            ->with([
                'vehicle:id,license_plate,model,year,status',
                'user:id,name,email,phone'
            ])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Get assignments that are about to expire or need attention.
     */
    public function alerts(): JsonResponse
    {
        $longTermAssignments = VehicleAssignment::active()
            ->where('assigned_at', '<=', Carbon::now()->subMonths(6))
            ->with([
                'vehicle:id,license_plate,model,year',
                'user:id,name,email'
            ])
            ->get();

        $unassignedVehicles = Vehicle::whereNotIn('id', function ($query) {
            $query->select('vehicle_id')
                  ->from('vehicles_assignments')
                  ->whereNull('unassigned_at');
        })
        ->where('status', 'active')
        ->get(['id', 'license_plate', 'model', 'year']);

        return response()->json([
            'success' => true,
            'data' => [
                'long_term_assignments' => $longTermAssignments,
                'unassigned_vehicles' => $unassignedVehicles
            ]
        ]);
    }

    /**
     * Get assignment statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_assignments' => VehicleAssignment::count(),
            'active_assignments' => VehicleAssignment::active()->count(),
            'inactive_assignments' => VehicleAssignment::inactive()->count(),
            'unassigned_vehicles' => Vehicle::whereNotIn('id', function ($query) {
                $query->select('vehicle_id')
                      ->from('vehicles_assignments')
                      ->whereNull('unassigned_at');
            })->where('status', 'active')->count(),
            'average_assignment_duration' => VehicleAssignment::inactive()
                ->selectRaw('AVG(DATEDIFF(unassigned_at, assigned_at)) as avg_days')
                ->value('avg_days'),
            'assignments_by_month' => VehicleAssignment::selectRaw('DATE_FORMAT(assigned_at, "%Y-%m") as month, COUNT(*) as count')
                ->where('assigned_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'top_users_by_assignments' => VehicleAssignment::selectRaw('user_id, COUNT(*) as assignment_count')
                ->with(['user:id,name'])
                ->groupBy('user_id')
                ->orderBy('assignment_count', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Transfer vehicle from one user to another.
     */
    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_assignment_id' => 'required|exists:vehicles_assignments,id',
            'new_user_id' => 'required|exists:users,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $currentAssignment = VehicleAssignment::findOrFail($validated['current_assignment_id']);

        if (!$currentAssignment->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Current assignment is not active'
            ], 422);
        }

        // Check if new user already has an active assignment
        $newUserActiveAssignment = VehicleAssignment::where('user_id', $validated['new_user_id'])
            ->active()
            ->first();

        if ($newUserActiveAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'New user already has an active vehicle assignment'
            ], 422);
        }

        // End current assignment
        $currentAssignment->update([
            'unassigned_at' => $validated['transfer_date'],
            'unassignment_reason' => 'Transfer to another user'
        ]);

        // Create new assignment
        $newAssignment = VehicleAssignment::create([
            'vehicle_id' => $currentAssignment->vehicle_id,
            'user_id' => $validated['new_user_id'],
            'assigned_at' => $validated['transfer_date'],
            'notes' => $validated['notes']
        ]);

        $newAssignment->load(['vehicle:id,license_plate,model', 'user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle transferred successfully',
            'data' => [
                'old_assignment' => $currentAssignment->fresh(),
                'new_assignment' => $newAssignment
            ]
        ]);
    }
}
