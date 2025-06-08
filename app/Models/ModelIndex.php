<?php

/**
 * Laravel Eloquent Models for Vehicle Management System
 * 
 * This file serves as an index of all available models in the system.
 * Each model represents a database table and provides methods for
 * interacting with the data.
 */

// Core Models
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Vehicle.php';
require_once __DIR__ . '/VehicleMake.php';

// Maintenance Models
require_once __DIR__ . '/MaintenanceType.php';
require_once __DIR__ . '/MaintenanceLog.php';

// Fuel Management
require_once __DIR__ . '/FuelLog.php';

// Vehicle Assignment
require_once __DIR__ . '/VehicleAssignment.php';

// Document Management
require_once __DIR__ . '/VehicleDocument.php';

/**
 * Model Relationships Overview:
 * 
 * User
 * ├── vehicleAssignments() -> VehicleAssignment (hasMany)
 * ├── assignedVehicles() -> Vehicle (belongsToMany through vehicle_assignments)
 * ├── maintenanceLogs() -> MaintenanceLog (hasMany as performed_by)
 * └── fuelLogs() -> FuelLog (hasMany as logged_by)
 * 
 * Vehicle
 * ├── make() -> VehicleMake (belongsTo)
 * ├── assignments() -> VehicleAssignment (hasMany)
 * ├── assignedUsers() -> User (belongsToMany through vehicle_assignments)
 * ├── maintenanceLogs() -> MaintenanceLog (hasMany)
 * ├── fuelLogs() -> FuelLog (hasMany)
 * └── documents() -> VehicleDocument (hasMany)
 * 
 * VehicleMake
 * └── vehicles() -> Vehicle (hasMany)
 * 
 * MaintenanceType
 * └── maintenanceLogs() -> MaintenanceLog (hasMany)
 * 
 * MaintenanceLog
 * ├── vehicle() -> Vehicle (belongsTo)
 * ├── maintenanceType() -> MaintenanceType (belongsTo)
 * └── performedBy() -> User (belongsTo)
 * 
 * FuelLog
 * ├── vehicle() -> Vehicle (belongsTo)
 * └── loggedBy() -> User (belongsTo)
 * 
 * VehicleAssignment
 * ├── vehicle() -> Vehicle (belongsTo)
 * └── user() -> User (belongsTo)
 * 
 * VehicleDocument
 * └── vehicle() -> Vehicle (belongsTo)
 */

/**
 * Constants Reference:
 * 
 * User Roles:
 * - User::ROLE_ADMIN = 'admin'
 * - User::ROLE_MANAGER = 'manager'
 * - User::ROLE_VIEWER = 'viewer'
 * 
 * Vehicle Status:
 * - Vehicle::STATUS_ACTIVE = 'active'
 * - Vehicle::STATUS_MAINTENANCE = 'maintenance'
 * - Vehicle::STATUS_INACTIVE = 'inactive'
 * - Vehicle::STATUS_SOLD = 'sold'
 * 
 * Fuel Types:
 * - Vehicle::FUEL_GASOLINE = 'gasoline'
 * - Vehicle::FUEL_DIESEL = 'diesel'
 * - Vehicle::FUEL_HYBRID = 'hybrid'
 * - Vehicle::FUEL_ELECTRIC = 'electric'
 * 
 * Transmission Types:
 * - Vehicle::TRANSMISSION_MANUAL = 'manual'
 * - Vehicle::TRANSMISSION_AUTOMATIC = 'automatic'
 * - Vehicle::TRANSMISSION_CVT = 'cvt'
 * 
 * Maintenance Categories:
 * - MaintenanceType::CATEGORY_ROUTINE = 'Routine'
 * - MaintenanceType::CATEGORY_PREVENTIVE = 'Preventive'
 * - MaintenanceType::CATEGORY_REPAIR = 'Repair'
 * - MaintenanceType::CATEGORY_INSPECTION = 'Inspection'
 * - MaintenanceType::CATEGORY_EMERGENCY = 'Emergency'
 * 
 * Maintenance Status:
 * - MaintenanceLog::STATUS_COMPLETED = 'completed'
 * - MaintenanceLog::STATUS_SCHEDULED = 'scheduled'
 * - MaintenanceLog::STATUS_IN_PROGRESS = 'in_progress'
 * - MaintenanceLog::STATUS_CANCELLED = 'cancelled'
 * 
 * Document Types:
 * - VehicleDocument::TYPE_INSURANCE = 'insurance'
 * - VehicleDocument::TYPE_REGISTRATION = 'registration'
 * - VehicleDocument::TYPE_INSPECTION = 'inspection'
 * - VehicleDocument::TYPE_WARRANTY = 'warranty'
 * - VehicleDocument::TYPE_MANUAL = 'manual'
 * - VehicleDocument::TYPE_RECEIPT = 'receipt'
 * - VehicleDocument::TYPE_OTHER = 'other'
 */
