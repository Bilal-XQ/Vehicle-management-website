<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FuelLogController extends Controller
{
    /**
     * Display a listing of fuel logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = FuelLog::with(['vehicle.make', 'loggedBy']);

        // Filter by vehicle
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by fuel type
        if ($request->has('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('fuel_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('fuel_date', '<=', $request->date_to);
        }

        // Filter by cost range
        if ($request->has('cost_min')) {
            $query->where('total_cost', '>=', $request->cost_min);
        }
        if ($request->has('cost_max')) {
            $query->where('total_cost', '<=', $request->cost_max);
        }

        // Filter by quantity range
        if ($request->has('quantity_min')) {
            $query->where('quantity', '>=', $request->quantity_min);
        }
        if ($request->has('quantity_max')) {
            $query->where('quantity', '<=', $request->quantity_max);
        }

        // Filter by full tank
        if ($request->has('is_full_tank')) {
            $query->where('is_full_tank', $request->boolean('is_full_tank'));
        }

        // Filter recent entries
        if ($request->boolean('recent')) {
            $query->recent($request->get('recent_days', 30));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('gas_station', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhereHas('vehicle', function($vehicleQuery) use ($search) {
                      $vehicleQuery->where('license_plate', 'like', '%' . $search . '%')
                                   ->orWhere('model', 'like', '%' . $search . '%');
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'fuel_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $fuelLogs = $query->paginate($perPage);

        // Add calculated fields to each fuel log
        $fuelLogs->getCollection()->transform(function ($fuelLog) {
            $fuelLog->fuel_efficiency = $fuelLog->fuel_efficiency;
            $fuelLog->cost_per_mile = $fuelLog->cost_per_mile;
            return $fuelLog;
        });

        return response()->json([
            'success' => true,
            'data' => $fuelLogs->items(),
            'pagination' => [
                'current_page' => $fuelLogs->currentPage(),
                'last_page' => $fuelLogs->lastPage(),
                'per_page' => $fuelLogs->perPage(),
                'total' => $fuelLogs->total()
            ]
        ]);
    }

    /**
     * Store a newly created fuel log
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'logged_by' => 'required|exists:users,id',
            'fuel_date' => 'required|date|before_or_equal:today',
            'odometer_reading' => 'required|integer|min:0',
            'quantity' => 'required|numeric|min:0.1',
            'price_per_unit' => 'required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'fuel_type' => ['required', Rule::in(FuelLog::getFuelTypes())],
            'gas_station' => 'nullable|string|max:255',
            'miles_driven' => 'nullable|integer|min:0',
            'is_full_tank' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate odometer reading is not less than previous reading
        $vehicle = Vehicle::find($request->vehicle_id);
        $lastFuelLog = $vehicle->fuelLogs()->orderBy('fuel_date', 'desc')->first();
        
        if ($lastFuelLog && $request->odometer_reading < $lastFuelLog->odometer_reading) {
            return response()->json([
                'success' => false,
                'message' => 'Odometer reading cannot be less than previous reading (' . $lastFuelLog->odometer_reading . ')'
            ], 422);
        }

        $fuelLog = FuelLog::create($request->all());
        $fuelLog->load(['vehicle.make', 'loggedBy']);

        // Add calculated fields
        $fuelLog->fuel_efficiency = $fuelLog->fuel_efficiency;
        $fuelLog->cost_per_mile = $fuelLog->cost_per_mile;

        return response()->json([
            'success' => true,
            'message' => 'Fuel log created successfully',
            'data' => $fuelLog
        ], 201);
    }

    /**
     * Display the specified fuel log
     */
    public function show(FuelLog $fuelLog): JsonResponse
    {
        $fuelLog->load(['vehicle.make', 'loggedBy']);

        // Add calculated fields
        $fuelLog->fuel_efficiency = $fuelLog->fuel_efficiency;
        $fuelLog->cost_per_mile = $fuelLog->cost_per_mile;

        return response()->json([
            'success' => true,
            'data' => $fuelLog
        ]);
    }

    /**
     * Update the specified fuel log
     */
    public function update(Request $request, FuelLog $fuelLog): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'logged_by' => 'sometimes|required|exists:users,id',
            'fuel_date' => 'sometimes|required|date|before_or_equal:today',
            'odometer_reading' => 'sometimes|required|integer|min:0',
            'quantity' => 'sometimes|required|numeric|min:0.1',
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'fuel_type' => ['sometimes', 'required', Rule::in(FuelLog::getFuelTypes())],
            'gas_station' => 'nullable|string|max:255',
            'miles_driven' => 'nullable|integer|min:0',
            'is_full_tank' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $fuelLog->update($request->all());
        $fuelLog->load(['vehicle.make', 'loggedBy']);

        // Add calculated fields
        $fuelLog->fuel_efficiency = $fuelLog->fuel_efficiency;
        $fuelLog->cost_per_mile = $fuelLog->cost_per_mile;

        return response()->json([
            'success' => true,
            'message' => 'Fuel log updated successfully',
            'data' => $fuelLog
        ]);
    }

    /**
     * Remove the specified fuel log
     */
    public function destroy(FuelLog $fuelLog): JsonResponse
    {
        $fuelLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fuel log deleted successfully'
        ]);
    }

    /**
     * Get fuel statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_fuel_logs' => FuelLog::count(),
            'total_fuel_cost' => FuelLog::sum('total_cost'),
            'total_fuel_quantity' => FuelLog::sum('quantity'),
            'average_fuel_price' => FuelLog::avg('price_per_unit'),
            'average_fuel_efficiency' => FuelLog::whereNotNull('miles_driven')
                                               ->where('miles_driven', '>', 0)
                                               ->where('quantity', '>', 0)
                                               ->get()
                                               ->filter(function($log) {
                                                   return $log->fuel_efficiency !== null;
                                               })
                                               ->avg('fuel_efficiency'),
            'fuel_logs_this_month' => FuelLog::whereBetween('fuel_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
            'fuel_cost_this_month' => FuelLog::whereBetween('fuel_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->sum('total_cost'),
            'fuel_by_type' => FuelLog::groupBy('fuel_type')
                                    ->selectRaw('fuel_type, COUNT(*) as count, SUM(total_cost) as total_cost')
                                    ->get()
                                    ->keyBy('fuel_type'),
            'top_gas_stations' => FuelLog::whereNotNull('gas_station')
                                        ->groupBy('gas_station')
                                        ->selectRaw('gas_station, COUNT(*) as visit_count, SUM(total_cost) as total_spent')
                                        ->orderBy('visit_count', 'desc')
                                        ->limit(10)
                                        ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get fuel efficiency trends
     */
    public function efficiencyTrends(Request $request): JsonResponse
    {
        $months = $request->get('months', 12);
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();

        $trends = FuelLog::with('vehicle.make')
                         ->where('fuel_date', '>=', $startDate)
                         ->where('is_full_tank', true)
                         ->whereNotNull('miles_driven')
                         ->where('miles_driven', '>', 0)
                         ->where('quantity', '>', 0)
                         ->get()
                         ->filter(function($log) {
                             return $log->fuel_efficiency !== null;
                         })
                         ->groupBy(function($log) {
                             return $log->fuel_date->format('Y-m');
                         })
                         ->map(function($logs, $month) {
                             return [
                                 'month' => $month,
                                 'average_efficiency' => round($logs->avg('fuel_efficiency'), 2),
                                 'total_logs' => $logs->count(),
                                 'total_cost' => round($logs->sum('total_cost'), 2),
                                 'total_miles' => $logs->sum('miles_driven'),
                             ];
                         })
                         ->values();

        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }

    /**
     * Get fuel history for a vehicle
     */
    public function vehicleHistory(Vehicle $vehicle): JsonResponse
    {
        $fuelHistory = $vehicle->fuelLogs()
                              ->with('loggedBy')
                              ->orderBy('fuel_date', 'desc')
                              ->get();

        // Add calculated fields
        $fuelHistory->transform(function ($fuelLog) {
            $fuelLog->fuel_efficiency = $fuelLog->fuel_efficiency;
            $fuelLog->cost_per_mile = $fuelLog->cost_per_mile;
            return $fuelLog;
        });

        $stats = [
            'total_fuel_records' => $fuelHistory->count(),
            'total_fuel_cost' => $fuelHistory->sum('total_cost'),
            'total_fuel_quantity' => $fuelHistory->sum('quantity'),
            'average_fuel_efficiency' => $fuelHistory->filter(function($log) {
                return $log->fuel_efficiency !== null;
            })->avg('fuel_efficiency'),
            'last_fuel_date' => $fuelHistory->first()?->fuel_date,
            'total_miles_tracked' => $fuelHistory->sum('miles_driven'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'fuel_history' => $fuelHistory,
                'statistics' => $stats
            ]
        ]);
    }

    /**
     * Get fuel cost analysis
     */
    public function costAnalysis(Request $request): JsonResponse
    {
        $months = $request->get('months', 6);
        $startDate = Carbon::now()->subMonths($months);

        $analysis = FuelLog::where('fuel_date', '>=', $startDate)
                          ->with('vehicle.make')
                          ->get()
                          ->groupBy(function($log) {
                              return $log->vehicle_id;
                          })
                          ->map(function($logs, $vehicleId) {
                              $vehicle = $logs->first()->vehicle;
                              return [
                                  'vehicle_id' => $vehicleId,
                                  'vehicle_name' => $vehicle->full_name,
                                  'license_plate' => $vehicle->license_plate,
                                  'total_cost' => round($logs->sum('total_cost'), 2),
                                  'total_quantity' => round($logs->sum('quantity'), 2),
                                  'total_fills' => $logs->count(),
                                  'average_cost_per_fill' => round($logs->avg('total_cost'), 2),
                                  'average_efficiency' => round($logs->filter(function($log) {
                                      return $log->fuel_efficiency !== null;
                                  })->avg('fuel_efficiency'), 2),
                              ];
                          })
                          ->sortByDesc('total_cost')
                          ->values();

        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }
}
