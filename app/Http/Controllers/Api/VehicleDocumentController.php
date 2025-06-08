<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleDocument;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VehicleDocumentController extends Controller
{
    /**
     * Display a listing of vehicle documents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VehicleDocument::with(['vehicle:id,license_plate,model,year']);

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('document_name', 'like', "%{$searchTerm}%")
                  ->orWhere('document_type', 'like', "%{$searchTerm}%")
                  ->orWhereHas('vehicle', function ($vehicleQuery) use ($searchTerm) {
                      $vehicleQuery->where('license_plate', 'like', "%{$searchTerm}%")
                                   ->orWhere('model', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter by vehicle
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by document type
        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by expiry status
        if ($request->has('expiry_status')) {
            switch ($request->expiry_status) {
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
                case 'valid':
                    $query->valid();
                    break;
            }
        }

        // Filter by date range
        if ($request->has('issue_date_from')) {
            $query->where('issue_date', '>=', Carbon::parse($request->issue_date_from));
        }
        if ($request->has('issue_date_to')) {
            $query->where('issue_date', '<=', Carbon::parse($request->issue_date_to));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['document_name', 'document_type', 'issue_date', 'expiry_date', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $documents = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $documents->items(),
            'pagination' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ]
        ]);
    }

    /**
     * Store a newly created vehicle document.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:registration,insurance,inspection,license,permit,other',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'document_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('vehicle-documents', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $document = VehicleDocument::create($validated);
        $document->load(['vehicle:id,license_plate,model,year']);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle document created successfully',
            'data' => $document
        ], 201);
    }

    /**
     * Display the specified vehicle document.
     */
    public function show(VehicleDocument $vehicleDocument): JsonResponse
    {
        $vehicleDocument->load(['vehicle:id,license_plate,model,year,vin']);

        return response()->json([
            'success' => true,
            'data' => $vehicleDocument
        ]);
    }

    /**
     * Update the specified vehicle document.
     */
    public function update(Request $request, VehicleDocument $vehicleDocument): JsonResponse
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:registration,insurance,inspection,license,permit,other',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'document_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($vehicleDocument->file_path && Storage::disk('public')->exists($vehicleDocument->file_path)) {
                Storage::disk('public')->delete($vehicleDocument->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('vehicle-documents', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $vehicleDocument->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle document updated successfully',
            'data' => $vehicleDocument->fresh(['vehicle:id,license_plate,model'])
        ]);
    }

    /**
     * Remove the specified vehicle document.
     */
    public function destroy(VehicleDocument $vehicleDocument): JsonResponse
    {
        // Delete file if exists
        if ($vehicleDocument->file_path && Storage::disk('public')->exists($vehicleDocument->file_path)) {
            Storage::disk('public')->delete($vehicleDocument->file_path);
        }

        $vehicleDocument->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle document deleted successfully'
        ]);
    }

    /**
     * Download the document file.
     */
    public function download(VehicleDocument $vehicleDocument): JsonResponse
    {
        if (!$vehicleDocument->file_path || !Storage::disk('public')->exists($vehicleDocument->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Document file not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => Storage::disk('public')->url($vehicleDocument->file_path),
                'file_name' => $vehicleDocument->file_name,
                'file_size' => $vehicleDocument->file_size,
                'mime_type' => $vehicleDocument->mime_type
            ]
        ]);
    }

    /**
     * Get documents for a specific vehicle.
     */
    public function vehicleDocuments(Vehicle $vehicle): JsonResponse
    {
        $documents = VehicleDocument::where('vehicle_id', $vehicle->id)
            ->orderBy('document_type')
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->groupBy('document_type');

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => $vehicle->only(['id', 'license_plate', 'model', 'year']),
                'documents' => $documents
            ]
        ]);
    }

    /**
     * Get expired documents.
     */
    public function expired(): JsonResponse
    {
        $expiredDocuments = VehicleDocument::expired()
            ->with(['vehicle:id,license_plate,model,year'])
            ->orderBy('expiry_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expiredDocuments
        ]);
    }

    /**
     * Get documents expiring soon.
     */
    public function expiringSoon(Request $request): JsonResponse
    {
        $days = $request->get('days', 30); // Default to 30 days
        
        $expiringSoonDocuments = VehicleDocument::expiringSoon($days)
            ->with(['vehicle:id,license_plate,model,year'])
            ->orderBy('expiry_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expiringSoonDocuments
        ]);
    }

    /**
     * Get document alerts and reminders.
     */
    public function alerts(): JsonResponse
    {
        $alerts = [
            'expired' => VehicleDocument::expired()
                ->with(['vehicle:id,license_plate,model'])
                ->count(),
            'expiring_soon' => VehicleDocument::expiringSoon(30)
                ->with(['vehicle:id,license_plate,model'])
                ->count(),
            'expiring_this_week' => VehicleDocument::expiringSoon(7)
                ->with(['vehicle:id,license_plate,model'])
                ->get(),
            'expired_details' => VehicleDocument::expired()
                ->with(['vehicle:id,license_plate,model'])
                ->orderBy('expiry_date', 'desc')
                ->take(10)
                ->get(),
            'missing_documents' => $this->getMissingDocuments()
        ];

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Get document statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_documents' => VehicleDocument::count(),
            'by_type' => VehicleDocument::selectRaw('document_type, COUNT(*) as count')
                ->groupBy('document_type')
                ->orderBy('count', 'desc')
                ->get(),
            'expiry_status' => [
                'expired' => VehicleDocument::expired()->count(),
                'expiring_soon' => VehicleDocument::expiringSoon(30)->count(),
                'valid' => VehicleDocument::valid()->count()
            ],
            'documents_by_month' => VehicleDocument::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'vehicles_with_documents' => VehicleDocument::distinct('vehicle_id')->count('vehicle_id'),
            'vehicles_without_documents' => Vehicle::whereNotIn('id', function ($query) {
                $query->select('vehicle_id')->from('vehicles_documents');
            })->count(),
            'average_documents_per_vehicle' => round(VehicleDocument::count() / max(Vehicle::count(), 1), 2),
            'storage_usage' => [
                'total_files' => VehicleDocument::whereNotNull('file_path')->count(),
                'total_size_mb' => round(VehicleDocument::sum('file_size') / (1024 * 1024), 2)
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Bulk upload documents.
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'documents' => 'required|array|min:1|max:10',
            'documents.*.document_name' => 'required|string|max:255',
            'documents.*.document_type' => 'required|in:registration,insurance,inspection,license,permit,other',
            'documents.*.issue_date' => 'required|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.document_number' => 'nullable|string|max:100',
            'documents.*.issuing_authority' => 'nullable|string|max:255',
            'documents.*.notes' => 'nullable|string|max:1000',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        $createdDocuments = [];
        $files = $request->file('files', []);

        foreach ($validated['documents'] as $index => $documentData) {
            $documentData['vehicle_id'] = $validated['vehicle_id'];

            // Handle file upload if provided
            if (isset($files[$index])) {
                $file = $files[$index];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('vehicle-documents', $fileName, 'public');
                
                $documentData['file_path'] = $filePath;
                $documentData['file_name'] = $fileName;
                $documentData['file_size'] = $file->getSize();
                $documentData['mime_type'] = $file->getMimeType();
            }

            $document = VehicleDocument::create($documentData);
            $createdDocuments[] = $document;
        }

        return response()->json([
            'success' => true,
            'message' => count($createdDocuments) . ' documents uploaded successfully',
            'data' => $createdDocuments
        ], 201);
    }

    /**
     * Get vehicles with missing essential documents.
     */
    private function getMissingDocuments(): array
    {
        $essentialDocuments = ['registration', 'insurance', 'inspection'];
        $vehicles = Vehicle::with(['documents' => function ($query) {
            $query->select('vehicle_id', 'document_type');
        }])->get(['id', 'license_plate', 'model']);

        $missingDocuments = [];

        foreach ($vehicles as $vehicle) {
            $existingTypes = $vehicle->documents->pluck('document_type')->toArray();
            $missing = array_diff($essentialDocuments, $existingTypes);
            
            if (!empty($missing)) {
                $missingDocuments[] = [
                    'vehicle' => $vehicle->only(['id', 'license_plate', 'model']),
                    'missing_documents' => $missing
                ];
            }
        }

        return $missingDocuments;
    }
}
