-- ========================================
-- MODERN VEHICLE MANAGEMENT SYSTEM
-- MySQL Database Schema
-- ========================================
-- Generated: June 8, 2025
-- Framework: Laravel 10+ with Eloquent ORM
-- Features: Soft Deletes, Foreign Keys, Indexes
-- ========================================

-- Create database
CREATE DATABASE IF NOT EXISTS vehicle_management 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE vehicle_management;

-- ========================================
-- TABLE: users
-- Purpose: System users with role-based access
-- ========================================
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'viewer') NOT NULL DEFAULT 'viewer',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: vehicle_makes
-- Purpose: Vehicle manufacturers/brands
-- ========================================
CREATE TABLE vehicle_makes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(255) NULL DEFAULT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_vehicle_makes_name (name),
    INDEX idx_vehicle_makes_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: vehicles
-- Purpose: Main vehicle registry with soft deletes
-- ========================================
CREATE TABLE vehicles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicle_make_id BIGINT UNSIGNED NOT NULL,
    model VARCHAR(255) NOT NULL,
    year YEAR NOT NULL,
    license_plate VARCHAR(255) NOT NULL UNIQUE,
    vin VARCHAR(255) NULL DEFAULT NULL UNIQUE,
    status ENUM('active', 'maintenance', 'retired', 'out_of_service') NOT NULL DEFAULT 'active',
    fuel_type ENUM('gasoline', 'diesel', 'electric', 'hybrid', 'lpg') NULL DEFAULT NULL,
    color VARCHAR(255) NULL DEFAULT NULL,
    mileage INT UNSIGNED NOT NULL DEFAULT 0,
    purchase_date DATE NULL DEFAULT NULL,
    purchase_price DECIMAL(10,2) NULL DEFAULT NULL,
    registration_expiry DATE NULL DEFAULT NULL,
    insurance_expiry DATE NULL DEFAULT NULL,
    notes TEXT NULL DEFAULT NULL,
    specifications JSON NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_vehicles_license_plate (license_plate),
    INDEX idx_vehicles_status (status),
    INDEX idx_vehicles_year (year),
    INDEX idx_vehicles_vehicle_make_id (vehicle_make_id),
    INDEX idx_vehicles_fuel_type (fuel_type),
    INDEX idx_vehicles_registration_expiry (registration_expiry),
    INDEX idx_vehicles_insurance_expiry (insurance_expiry),
    INDEX idx_vehicles_deleted_at (deleted_at),
    CONSTRAINT fk_vehicles_vehicle_make_id 
        FOREIGN KEY (vehicle_make_id) 
        REFERENCES vehicle_makes(id) 
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: maintenance_types
-- Purpose: Predefined maintenance categories
-- ========================================
CREATE TABLE maintenance_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL DEFAULT NULL,
    recommended_interval_km INT UNSIGNED NULL DEFAULT NULL,
    recommended_interval_months INT UNSIGNED NULL DEFAULT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_maintenance_types_name (name),
    INDEX idx_maintenance_types_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: maintenance_logs
-- Purpose: Vehicle maintenance history tracking
-- ========================================
CREATE TABLE maintenance_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    maintenance_type_id BIGINT UNSIGNED NULL DEFAULT NULL,
    performed_by BIGINT UNSIGNED NULL DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    performed_at DATE NOT NULL,
    mileage_at_service INT UNSIGNED NOT NULL,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    service_provider VARCHAR(255) NULL DEFAULT NULL,
    priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'completed',
    next_due_date DATE NULL DEFAULT NULL,
    next_due_mileage INT UNSIGNED NULL DEFAULT NULL,
    parts_used JSON NULL DEFAULT NULL,
    attachments JSON NULL DEFAULT NULL,
    notes TEXT NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_maintenance_logs_vehicle_id (vehicle_id),
    INDEX idx_maintenance_logs_maintenance_type_id (maintenance_type_id),
    INDEX idx_maintenance_logs_performed_by (performed_by),
    INDEX idx_maintenance_logs_performed_at (performed_at),
    INDEX idx_maintenance_logs_status (status),
    INDEX idx_maintenance_logs_priority (priority),
    INDEX idx_maintenance_logs_next_due_date (next_due_date),
    INDEX idx_maintenance_logs_vehicle_performed_at (vehicle_id, performed_at),
    CONSTRAINT fk_maintenance_logs_vehicle_id 
        FOREIGN KEY (vehicle_id) 
        REFERENCES vehicles(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_maintenance_logs_maintenance_type_id 
        FOREIGN KEY (maintenance_type_id) 
        REFERENCES maintenance_types(id) 
        ON DELETE SET NULL,
    CONSTRAINT fk_maintenance_logs_performed_by 
        FOREIGN KEY (performed_by) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: fuel_logs
-- Purpose: Vehicle fuel consumption tracking
-- ========================================
CREATE TABLE fuel_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    logged_by BIGINT UNSIGNED NULL DEFAULT NULL,
    fueled_at DATE NOT NULL,
    mileage INT UNSIGNED NOT NULL,
    liters DECIMAL(8,2) NOT NULL,
    cost_per_liter DECIMAL(8,3) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    fuel_station VARCHAR(255) NULL DEFAULT NULL,
    full_tank BOOLEAN NOT NULL DEFAULT TRUE,
    notes TEXT NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_fuel_logs_vehicle_id (vehicle_id),
    INDEX idx_fuel_logs_logged_by (logged_by),
    INDEX idx_fuel_logs_fueled_at (fueled_at),
    INDEX idx_fuel_logs_vehicle_fueled_at (vehicle_id, fueled_at),
    CONSTRAINT fk_fuel_logs_vehicle_id 
        FOREIGN KEY (vehicle_id) 
        REFERENCES vehicles(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_fuel_logs_logged_by 
        FOREIGN KEY (logged_by) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: vehicle_assignments
-- Purpose: Track vehicle assignments to users
-- ========================================
CREATE TABLE vehicle_assignments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    assigned_to BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NOT NULL,
    assigned_at DATE NOT NULL,
    assigned_until DATE NULL DEFAULT NULL,
    status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
    purpose TEXT NULL DEFAULT NULL,
    notes TEXT NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_vehicle_assignments_vehicle_id (vehicle_id),
    INDEX idx_vehicle_assignments_assigned_to (assigned_to),
    INDEX idx_vehicle_assignments_assigned_by (assigned_by),
    INDEX idx_vehicle_assignments_status (status),
    INDEX idx_vehicle_assignments_assigned_at (assigned_at),
    INDEX idx_vehicle_assignments_vehicle_status (vehicle_id, status),
    CONSTRAINT fk_vehicle_assignments_vehicle_id 
        FOREIGN KEY (vehicle_id) 
        REFERENCES vehicles(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_assignments_assigned_to 
        FOREIGN KEY (assigned_to) 
        REFERENCES users(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_assignments_assigned_by 
        FOREIGN KEY (assigned_by) 
        REFERENCES users(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: vehicle_documents
-- Purpose: Store vehicle-related documents
-- ========================================
CREATE TABLE vehicle_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    uploaded_by BIGINT UNSIGNED NULL DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    type ENUM('registration', 'insurance', 'inspection', 'manual', 'receipt', 'other') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    expiry_date DATE NULL DEFAULT NULL,
    description TEXT NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_vehicle_documents_vehicle_id (vehicle_id),
    INDEX idx_vehicle_documents_uploaded_by (uploaded_by),
    INDEX idx_vehicle_documents_type (type),
    INDEX idx_vehicle_documents_expiry_date (expiry_date),
    CONSTRAINT fk_vehicle_documents_vehicle_id 
        FOREIGN KEY (vehicle_id) 
        REFERENCES vehicles(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_documents_uploaded_by 
        FOREIGN KEY (uploaded_by) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLE: personal_access_tokens
-- Purpose: Laravel Sanctum authentication tokens
-- ========================================
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL DEFAULT NULL,
    last_used_at TIMESTAMP NULL DEFAULT NULL,
    expires_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_personal_access_tokens_tokenable (tokenable_type, tokenable_id),
    INDEX idx_personal_access_tokens_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SAMPLE DATA INSERTS
-- ========================================

-- Insert sample vehicle makes
INSERT INTO vehicle_makes (name, country, is_active, created_at, updated_at) VALUES
('Toyota', 'Japan', 1, NOW(), NOW()),
('Honda', 'Japan', 1, NOW(), NOW()),
('Ford', 'USA', 1, NOW(), NOW()),
('Chevrolet', 'USA', 1, NOW(), NOW()),
('BMW', 'Germany', 1, NOW(), NOW()),
('Mercedes-Benz', 'Germany', 1, NOW(), NOW()),
('Volkswagen', 'Germany', 1, NOW(), NOW()),
('Tesla', 'USA', 1, NOW(), NOW()),
('Nissan', 'Japan', 1, NOW(), NOW()),
('Hyundai', 'South Korea', 1, NOW(), NOW());

-- Insert sample users (password is 'password123' hashed)
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) VALUES
('Admin User', 'admin@vehiclemanagement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW(), NOW()),
('Fleet Manager', 'manager@vehiclemanagement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 1, NOW(), NOW(), NOW()),
('John Smith', 'john@vehiclemanagement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'viewer', 1, NOW(), NOW(), NOW()),
('Sarah Johnson', 'sarah@vehiclemanagement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'viewer', 1, NOW(), NOW(), NOW());

-- Insert sample maintenance types
INSERT INTO maintenance_types (name, description, recommended_interval_km, recommended_interval_months, is_active, created_at, updated_at) VALUES
('Oil Change', 'Regular engine oil and filter change', 10000, 6, 1, NOW(), NOW()),
('Tire Rotation', 'Rotate tires to ensure even wear', 12000, 6, 1, NOW(), NOW()),
('Brake Inspection', 'Check brake pads, rotors, and fluid', 20000, 12, 1, NOW(), NOW()),
('Air Filter Replacement', 'Replace engine air filter', 20000, 12, 1, NOW(), NOW()),
('Transmission Service', 'Transmission fluid change and inspection', 60000, 24, 1, NOW(), NOW()),
('Coolant Flush', 'Replace engine coolant', 60000, 24, 1, NOW(), NOW()),
('Spark Plug Replacement', 'Replace spark plugs', 50000, 36, 1, NOW(), NOW()),
('Battery Test', 'Test battery and charging system', NULL, 12, 1, NOW(), NOW()),
('Wheel Alignment', 'Adjust wheel alignment', 20000, NULL, 1, NOW(), NOW()),
('General Inspection', 'Comprehensive vehicle inspection', 20000, 12, 1, NOW(), NOW());

-- Insert sample vehicles
INSERT INTO vehicles (vehicle_make_id, model, year, license_plate, vin, status, fuel_type, color, mileage, purchase_date, purchase_price, registration_expiry, insurance_expiry, created_at, updated_at) VALUES
(1, 'Camry', 2022, 'ABC-1234', '1HGBH41JXMN109186', 'active', 'gasoline', 'Silver', 15000, '2022-01-15', 28000.00, '2025-01-15', '2025-01-15', NOW(), NOW()),
(2, 'Civic', 2023, 'XYZ-5678', '2HGFC2F59NH123456', 'active', 'gasoline', 'Blue', 8500, '2023-03-10', 25000.00, '2026-03-10', '2026-03-10', NOW(), NOW()),
(3, 'F-150', 2021, 'DEF-9012', '1FTFW1ET5MFC12345', 'maintenance', 'gasoline', 'White', 35000, '2021-08-20', 35000.00, '2024-08-20', '2024-08-20', NOW(), NOW()),
(5, '320i', 2022, 'GHI-3456', 'WBA5A5C54KA123456', 'active', 'gasoline', 'Black', 18000, '2022-05-12', 42000.00, '2025-05-12', '2025-05-12', NOW(), NOW()),
(8, 'Model 3', 2023, 'ELE-0001', '5YJ3E1EA9NF123456', 'active', 'electric', 'White', 5000, '2023-06-01', 45000.00, '2026-06-01', '2026-06-01', NOW(), NOW());

-- ========================================
-- PERFORMANCE OPTIMIZATION VIEWS
-- ========================================

-- View for vehicle summary with make information
CREATE VIEW vehicle_summary AS
SELECT 
    v.id,
    v.license_plate,
    vm.name AS make_name,
    v.model,
    v.year,
    v.status,
    v.fuel_type,
    v.mileage,
    v.registration_expiry,
    v.insurance_expiry,
    CASE 
        WHEN v.registration_expiry < CURDATE() THEN 'expired'
        WHEN v.registration_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
        ELSE 'valid'
    END AS registration_status,
    CASE 
        WHEN v.insurance_expiry < CURDATE() THEN 'expired'
        WHEN v.insurance_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
        ELSE 'valid'
    END AS insurance_status
FROM vehicles v
JOIN vehicle_makes vm ON v.vehicle_make_id = vm.id
WHERE v.deleted_at IS NULL;

-- View for maintenance summary
CREATE VIEW maintenance_summary AS
SELECT 
    v.id AS vehicle_id,
    v.license_plate,
    COUNT(ml.id) AS total_maintenance_count,
    MAX(ml.performed_at) AS last_maintenance_date,
    SUM(ml.cost) AS total_maintenance_cost,
    AVG(ml.cost) AS average_maintenance_cost
FROM vehicles v
LEFT JOIN maintenance_logs ml ON v.id = ml.vehicle_id
WHERE v.deleted_at IS NULL
GROUP BY v.id, v.license_plate;

-- View for fuel efficiency tracking
CREATE VIEW fuel_efficiency AS
SELECT 
    v.id AS vehicle_id,
    v.license_plate,
    COUNT(fl.id) AS fuel_log_count,
    SUM(fl.liters) AS total_liters,
    SUM(fl.total_cost) AS total_fuel_cost,
    AVG(fl.cost_per_liter) AS average_cost_per_liter,
    MAX(fl.fueled_at) AS last_fuel_date
FROM vehicles v
LEFT JOIN fuel_logs fl ON v.id = fl.vehicle_id
WHERE v.deleted_at IS NULL
GROUP BY v.id, v.license_plate;

-- ========================================
-- STORED PROCEDURES
-- ========================================

DELIMITER //

-- Procedure to get vehicle dashboard statistics
CREATE PROCEDURE GetVehicleDashboardStats()
BEGIN
    SELECT 
        COUNT(*) as total_vehicles,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_vehicles,
        SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_vehicles,
        SUM(CASE WHEN status = 'retired' THEN 1 ELSE 0 END) as retired_vehicles,
        SUM(CASE WHEN registration_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_registrations,
        SUM(CASE WHEN insurance_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_insurance
    FROM vehicles 
    WHERE deleted_at IS NULL;
END //

-- Procedure to get vehicles due for maintenance
CREATE PROCEDURE GetVehiclesDueForMaintenance()
BEGIN
    SELECT DISTINCT
        v.id,
        v.license_plate,
        vm.name AS make_name,
        v.model,
        v.mileage,
        ml.next_due_date,
        ml.next_due_mileage,
        mt.name AS maintenance_type
    FROM vehicles v
    JOIN vehicle_makes vm ON v.vehicle_make_id = vm.id
    LEFT JOIN maintenance_logs ml ON v.id = ml.vehicle_id
    LEFT JOIN maintenance_types mt ON ml.maintenance_type_id = mt.id
    WHERE v.deleted_at IS NULL
    AND v.status = 'active'
    AND (
        (ml.next_due_date IS NOT NULL AND ml.next_due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY))
        OR 
        (ml.next_due_mileage IS NOT NULL AND v.mileage >= ml.next_due_mileage)
    )
    ORDER BY ml.next_due_date ASC, ml.next_due_mileage ASC;
END //

DELIMITER ;

-- ========================================
-- TRIGGERS
-- ========================================

DELIMITER //

-- Trigger to update vehicle mileage when maintenance is logged
CREATE TRIGGER update_vehicle_mileage_after_maintenance
    AFTER INSERT ON maintenance_logs
    FOR EACH ROW
BEGIN
    UPDATE vehicles 
    SET mileage = GREATEST(mileage, NEW.mileage_at_service)
    WHERE id = NEW.vehicle_id;
END //

-- Trigger to update vehicle mileage when fuel is logged
CREATE TRIGGER update_vehicle_mileage_after_fuel
    AFTER INSERT ON fuel_logs
    FOR EACH ROW
BEGIN
    UPDATE vehicles 
    SET mileage = GREATEST(mileage, NEW.mileage)
    WHERE id = NEW.vehicle_id;
END //

DELIMITER ;

-- ========================================
-- ADDITIONAL INDEXES FOR PERFORMANCE
-- ========================================

-- Composite indexes for common queries
CREATE INDEX idx_vehicles_status_make ON vehicles(status, vehicle_make_id);
CREATE INDEX idx_maintenance_logs_vehicle_status ON maintenance_logs(vehicle_id, status);
CREATE INDEX idx_fuel_logs_date_range ON fuel_logs(vehicle_id, fueled_at);

-- ========================================
-- END OF SCHEMA
-- ========================================