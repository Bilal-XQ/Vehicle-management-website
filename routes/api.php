<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleMakeController;
use App\Http\Controllers\Api\MaintenanceTypeController;
use App\Http\Controllers\Api\MaintenanceLogController;
use App\Http\Controllers\Api\FuelLogController;
use App\Http\Controllers\Api\VehicleAssignmentController;
use App\Http\Controllers\Api\VehicleDocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

// Protected routes requiring authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/refresh-token', [UserController::class, 'refreshToken']);
        Route::get('/permissions', [UserController::class, 'permissions']);
    });

    // User management routes
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::get('users-statistics', [UserController::class, 'statistics']);

    // Vehicle Make routes
    Route::apiResource('vehicle-makes', VehicleMakeController::class);
    Route::get('vehicle-makes-options', [VehicleMakeController::class, 'options']);
    Route::patch('vehicle-makes/{vehicleMake}/toggle-status', [VehicleMakeController::class, 'toggleStatus']);
    Route::get('vehicle-makes-statistics', [VehicleMakeController::class, 'statistics']);

    // Maintenance Type routes
    Route::apiResource('maintenance-types', MaintenanceTypeController::class);
    Route::get('maintenance-types-options', [MaintenanceTypeController::class, 'options']);
    Route::patch('maintenance-types/{maintenanceType}/toggle-status', [MaintenanceTypeController::class, 'toggleStatus']);
    Route::get('maintenance-types-statistics', [MaintenanceTypeController::class, 'statistics']);
    Route::get('maintenance-types-due-for-vehicle', [MaintenanceTypeController::class, 'dueForVehicle']);

    // Vehicle routes
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('vehicles/{vehicle}/maintenance-history', [VehicleController::class, 'maintenanceHistory']);
    Route::get('vehicles/{vehicle}/fuel-history', [VehicleController::class, 'fuelHistory']);
    Route::get('vehicles/{vehicle}/assignment-history', [VehicleController::class, 'assignmentHistory']);
    Route::patch('vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus']);
    Route::get('vehicles-statistics', [VehicleController::class, 'statistics']);
    Route::get('vehicles-alerts', [VehicleController::class, 'alerts']);

    // Vehicle Assignment routes
    Route::apiResource('vehicle-assignments', VehicleAssignmentController::class);
    Route::get('vehicle-assignments/vehicle/{vehicle}/history', [VehicleAssignmentController::class, 'vehicleHistory']);
    Route::get('vehicle-assignments/user/{user}/history', [VehicleAssignmentController::class, 'userHistory']);
    Route::get('vehicle-assignments-active', [VehicleAssignmentController::class, 'active']);
    Route::get('vehicle-assignments-alerts', [VehicleAssignmentController::class, 'alerts']);
    Route::get('vehicle-assignments-statistics', [VehicleAssignmentController::class, 'statistics']);
    Route::post('vehicle-assignments-transfer', [VehicleAssignmentController::class, 'transfer']);

    // Vehicle Document routes
    Route::apiResource('vehicle-documents', VehicleDocumentController::class);
    Route::get('vehicle-documents/{vehicleDocument}/download', [VehicleDocumentController::class, 'download']);
    Route::get('vehicle-documents/vehicle/{vehicle}/documents', [VehicleDocumentController::class, 'vehicleDocuments']);
    Route::get('vehicle-documents-expired', [VehicleDocumentController::class, 'expired']);
    Route::get('vehicle-documents-expiring-soon', [VehicleDocumentController::class, 'expiringSoon']);
    Route::get('vehicle-documents-alerts', [VehicleDocumentController::class, 'alerts']);
    Route::get('vehicle-documents-statistics', [VehicleDocumentController::class, 'statistics']);
    Route::post('vehicle-documents-bulk-upload', [VehicleDocumentController::class, 'bulkUpload']);

    // Maintenance Log routes
    Route::apiResource('maintenance-logs', MaintenanceLogController::class);
    Route::get('maintenance-logs/{maintenanceLog}/details', [MaintenanceLogController::class, 'details']);
    Route::patch('maintenance-logs/{maintenanceLog}/status', [MaintenanceLogController::class, 'updateStatus']);
    Route::get('maintenance-logs-statistics', [MaintenanceLogController::class, 'statistics']);
    Route::get('maintenance-logs-alerts', [MaintenanceLogController::class, 'alerts']);
    Route::get('maintenance-logs-upcoming', [MaintenanceLogController::class, 'upcoming']);
    Route::get('maintenance-logs-overdue', [MaintenanceLogController::class, 'overdue']);

    // Fuel Log routes
    Route::apiResource('fuel-logs', FuelLogController::class);
    Route::get('fuel-logs/{fuelLog}/details', [FuelLogController::class, 'details']);
    Route::get('fuel-logs-statistics', [FuelLogController::class, 'statistics']);
    Route::get('fuel-logs-efficiency-report', [FuelLogController::class, 'efficiencyReport']);
    Route::get('fuel-logs-cost-analysis', [FuelLogController::class, 'costAnalysis']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'vehicles' => [
                        'total' => \App\Models\Vehicle::count(),
                        'active' => \App\Models\Vehicle::where('status', 'active')->count(),
                        'in_maintenance' => \App\Models\Vehicle::where('status', 'maintenance')->count(),
                        'out_of_service' => \App\Models\Vehicle::where('status', 'out_of_service')->count()
                    ],
                    'maintenance' => [
                        'total_logs' => \App\Models\MaintenanceLog::count(),
                        'pending' => \App\Models\MaintenanceLog::where('status', 'pending')->count(),
                        'in_progress' => \App\Models\MaintenanceLog::where('status', 'in_progress')->count(),
                        'completed' => \App\Models\MaintenanceLog::where('status', 'completed')->count()
                    ],
                    'fuel' => [
                        'total_logs' => \App\Models\FuelLog::count(),
                        'total_cost' => \App\Models\FuelLog::sum('total_cost'),
                        'total_liters' => \App\Models\FuelLog::sum('liters'),
                        'avg_efficiency' => \App\Models\FuelLog::avg('fuel_efficiency')
                    ],
                    'users' => [
                        'total' => \App\Models\User::count(),
                        'active' => \App\Models\User::where('is_active', true)->count(),
                        'drivers' => \App\Models\User::where('role', 'driver')->count()
                    ],
                    'assignments' => [
                        'active' => \App\Models\VehicleAssignment::whereNull('unassigned_at')->count(),
                        'unassigned_vehicles' => \App\Models\Vehicle::whereNotIn('id', function ($query) {
                            $query->select('vehicle_id')
                                  ->from('vehicles_assignments')
                                  ->whereNull('unassigned_at');
                        })->where('status', 'active')->count()
                    ],
                    'documents' => [
                        'total' => \App\Models\VehicleDocument::count(),
                        'expired' => \App\Models\VehicleDocument::where('expiry_date', '<', now())->count(),
                        'expiring_soon' => \App\Models\VehicleDocument::whereBetween('expiry_date', [now(), now()->addDays(30)])->count()
                    ]
                ]
            ]);
        });

        Route::get('/recent-activities', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'recent_maintenance' => \App\Models\MaintenanceLog::with(['vehicle:id,license_plate,model', 'user:id,name'])
                        ->latest()
                        ->take(10)
                        ->get(),
                    'recent_fuel_logs' => \App\Models\FuelLog::with(['vehicle:id,license_plate,model', 'user:id,name'])
                        ->latest()
                        ->take(10)
                        ->get(),
                    'recent_assignments' => \App\Models\VehicleAssignment::with(['vehicle:id,license_plate,model', 'user:id,name'])
                        ->latest()
                        ->take(10)
                        ->get()
                ]
            ]);
        });

        Route::get('/alerts', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'overdue_maintenance' => \App\Models\MaintenanceLog::where('scheduled_date', '<', now())
                        ->where('status', '!=', 'completed')
                        ->with(['vehicle:id,license_plate,model', 'maintenanceType:id,name'])
                        ->get(),
                    'expired_documents' => \App\Models\VehicleDocument::where('expiry_date', '<', now())
                        ->with(['vehicle:id,license_plate,model'])
                        ->get(),
                    'vehicles_needing_attention' => \App\Models\Vehicle::whereIn('status', ['maintenance', 'out_of_service'])
                        ->get(['id', 'license_plate', 'model', 'status']),
                    'long_term_assignments' => \App\Models\VehicleAssignment::whereNull('unassigned_at')
                        ->where('assigned_at', '<=', now()->subMonths(6))
                        ->with(['vehicle:id,license_plate,model', 'user:id,name'])
                        ->get()
                ]
            ]);
        });
    });

    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/vehicle-utilization', [VehicleController::class, 'utilizationReport']);
        Route::get('/maintenance-costs', [MaintenanceLogController::class, 'costReport']);
        Route::get('/fuel-consumption', [FuelLogController::class, 'consumptionReport']);
        Route::get('/vehicle-performance', [VehicleController::class, 'performanceReport']);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
