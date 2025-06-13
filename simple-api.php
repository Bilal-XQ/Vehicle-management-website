<?php
// Simple API endpoint for testing vehicle management functionality

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to set CORS headers
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

// Set CORS headers immediately
setCorsHeaders();

// Handle preflight OPTIONS request immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Ensure CORS headers are set again for OPTIONS
    setCorsHeaders();
    http_response_code(200);
    header('Content-Length: 0');
    exit();
}

// Set content type after OPTIONS handling
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// Remove query string if present
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/simple-api.php', '', $path);

// Mock vehicle data
$mockVehicles = [
    [
        'id' => 1,
        'make' => 'Toyota',
        'model' => 'Camry',
        'year' => 2022,
        'license_plate' => 'ABC-123',
        'status' => 'active',
        'mileage' => 15000,
        'color' => 'White',
        'vin' => '1HGBH41JXMN109186',
        'notes' => 'Regular maintenance vehicle',
        'created_at' => '2024-01-15T10:30:00Z',
        'updated_at' => '2024-01-15T10:30:00Z'
    ],
    [
        'id' => 2,
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2021,
        'license_plate' => 'XYZ-789',
        'status' => 'maintenance',
        'mileage' => 25000,
        'color' => 'Blue',
        'vin' => '2HGFC2F59KH123456',
        'notes' => 'In for oil change',
        'created_at' => '2024-01-10T14:20:00Z',
        'updated_at' => '2024-01-10T14:20:00Z'
    ],
    [
        'id' => 3,
        'make' => 'Ford',
        'model' => 'F-150',
        'year' => 2023,
        'license_plate' => 'DEF-456',
        'status' => 'active',
        'mileage' => 8000,
        'color' => 'Red',
        'vin' => '1FTFW1ET5NKF12345',
        'notes' => 'New fleet addition',
        'created_at' => '2024-01-20T09:15:00Z',
        'updated_at' => '2024-01-20T09:15:00Z'
    ],
    [
        'id' => 4,
        'make' => 'Chevrolet',
        'model' => 'Silverado',
        'year' => 2020,
        'license_plate' => 'GHI-789',
        'status' => 'inactive',
        'mileage' => 45000,
        'color' => 'Black',
        'vin' => '1GCUYEED5LZ123456',
        'notes' => 'Scheduled for retirement',
        'created_at' => '2024-01-05T16:45:00Z',
        'updated_at' => '2024-01-05T16:45:00Z'
    ],
    [
        'id' => 5,
        'make' => 'Nissan',
        'model' => 'Altima',
        'year' => 2021,
        'license_plate' => 'JKL-012',
        'status' => 'active',
        'mileage' => 22000,
        'color' => 'Silver',
        'vin' => '1N4BL4BV5MN123456',
        'notes' => 'Executive vehicle',
        'created_at' => '2024-01-12T11:30:00Z',
        'updated_at' => '2024-01-12T11:30:00Z'
    ]
];

// Mock vehicle makes for dropdown
$vehicleMakes = [
    ['id' => 1, 'name' => 'Toyota', 'country' => 'Japan'],
    ['id' => 2, 'name' => 'Honda', 'country' => 'Japan'],
    ['id' => 3, 'name' => 'Ford', 'country' => 'USA'],
    ['id' => 4, 'name' => 'Chevrolet', 'country' => 'USA'],
    ['id' => 5, 'name' => 'Nissan', 'country' => 'Japan'],
    ['id' => 6, 'name' => 'BMW', 'country' => 'Germany'],
    ['id' => 7, 'name' => 'Mercedes-Benz', 'country' => 'Germany']
];

// Simple routing
if ($method === 'POST' && $path === '/api/auth/login') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Simple validation for testing
    if (isset($input['email']) && isset($input['password'])) {
        // Mock authentication - accept any email/password for testing
        $response = [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => 'mock-token-' . time(),
                'user' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => $input['email'],
                    'role' => 'admin'
                ]
            ]
        ];
    } else {
        http_response_code(400);
        $response = [
            'success' => false,
            'message' => 'Email and password are required'
        ];
    }
    
    echo json_encode($response);
    exit();
}

// Get all vehicles with pagination and filtering
if ($method === 'GET' && $path === '/api/vehicles') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $make = isset($_GET['make']) ? $_GET['make'] : '';

    $filteredVehicles = $mockVehicles;

    // Apply search filter
    if (!empty($search)) {
        $filteredVehicles = array_filter($filteredVehicles, function($vehicle) use ($search) {
            return stripos($vehicle['make'], $search) !== false ||
                   stripos($vehicle['model'], $search) !== false ||
                   stripos($vehicle['license_plate'], $search) !== false;
        });
    }

    // Apply status filter
    if (!empty($status)) {
        $filteredVehicles = array_filter($filteredVehicles, function($vehicle) use ($status) {
            return $vehicle['status'] === $status;
        });
    }

    // Apply make filter
    if (!empty($make)) {
        $filteredVehicles = array_filter($filteredVehicles, function($vehicle) use ($make) {
            return $vehicle['make'] === $make;
        });
    }

    $total = count($filteredVehicles);
    $offset = ($page - 1) * $limit;
    $paginatedVehicles = array_slice($filteredVehicles, $offset, $limit);

    echo json_encode([
        'success' => true,
        'data' => array_values($paginatedVehicles),
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
    exit();
}

// Get single vehicle
if ($method === 'GET' && preg_match('/^\/api\/vehicles\/(\d+)$/', $path, $matches)) {
    $id = (int)$matches[1];
    $vehicle = array_filter($mockVehicles, function($v) use ($id) {
        return $v['id'] === $id;
    });
    
    if (!empty($vehicle)) {
        echo json_encode([
            'success' => true,
            'data' => array_values($vehicle)[0]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
    }
    exit();
}

// Create new vehicle
if ($method === 'POST' && $path === '/api/vehicles') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Basic validation
    $required = ['make', 'model', 'year', 'license_plate'];
    $errors = [];
    
    foreach ($required as $field) {
        if (empty($input[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ]);
        exit();
    }
    
    // Create new vehicle
    $newVehicle = [
        'id' => count($mockVehicles) + 1,
        'make' => $input['make'],
        'model' => $input['model'],
        'year' => (int)$input['year'],
        'license_plate' => $input['license_plate'],
        'status' => $input['status'] ?? 'active',
        'mileage' => (int)($input['mileage'] ?? 0),
        'color' => $input['color'] ?? '',
        'vin' => $input['vin'] ?? '',
        'notes' => $input['notes'] ?? '',
        'created_at' => date('c'),
        'updated_at' => date('c')
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Vehicle created successfully',
        'data' => $newVehicle
    ]);
    exit();
}

// Update vehicle
if ($method === 'PUT' && preg_match('/^\/api\/vehicles\/(\d+)$/', $path, $matches)) {
    $id = (int)$matches[1];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Find vehicle
    $vehicle = array_filter($mockVehicles, function($v) use ($id) {
        return $v['id'] === $id;
    });
    
    if (empty($vehicle)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
        exit();
    }
    
    $vehicle = array_values($vehicle)[0];
    
    // Update vehicle data
    foreach (['make', 'model', 'year', 'license_plate', 'status', 'mileage', 'color', 'vin', 'notes'] as $field) {
        if (isset($input[$field])) {
            $vehicle[$field] = $input[$field];
        }
    }
    $vehicle['updated_at'] = date('c');
    
    echo json_encode([
        'success' => true,
        'message' => 'Vehicle updated successfully',
        'data' => $vehicle
    ]);
    exit();
}

// Delete vehicle
if ($method === 'DELETE' && preg_match('/^\/api\/vehicles\/(\d+)$/', $path, $matches)) {
    $id = (int)$matches[1];
    
    echo json_encode([
        'success' => true,
        'message' => 'Vehicle deleted successfully'
    ]);
    exit();
}

// Get vehicle statistics
if ($method === 'GET' && $path === '/api/vehicles/stats') {
    $totalVehicles = count($mockVehicles);
    $activeVehicles = count(array_filter($mockVehicles, function($v) { return $v['status'] === 'active'; }));
    $maintenanceVehicles = count(array_filter($mockVehicles, function($v) { return $v['status'] === 'maintenance'; }));
    $inactiveVehicles = count(array_filter($mockVehicles, function($v) { return $v['status'] === 'inactive'; }));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total' => $totalVehicles,
            'active' => $activeVehicles,
            'maintenance' => $maintenanceVehicles,
            'inactive' => $inactiveVehicles
        ]
    ]);
    exit();
}

// Get vehicle makes
if ($method === 'GET' && $path === '/api/vehicle-makes') {
    echo json_encode([
        'success' => true,
        'data' => $vehicleMakes
    ]);
    exit();
}

// Default 404 response
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Endpoint not found'
]);
?>
