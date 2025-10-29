-- ============================================
-- USER AUTHENTICATION AND DETAILS TABLES
-- ============================================

-- User Credentials Table (Login & Role)
CREATE TABLE IF NOT EXISTS user_credentials (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') NOT NULL DEFAULT 'client',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- User Details Table (Extended Information)
CREATE TABLE IF NOT EXISTS user_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    company_name VARCHAR(200),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state_province VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    profile_image VARCHAR(500),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_credentials(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- ============================================
-- SAMPLE USER DATA
-- ============================================

-- Insert Sample Admin User
-- Username: admin
-- Password: admin123
-- Password Hash generated using PHP: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO user_credentials (username, email, password_hash, role, is_active) VALUES
('admin', 'admin@redymed.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE);

-- Insert Admin Details
INSERT INTO user_details (user_id, first_name, last_name, phone, company_name, city, country) VALUES
(1, 'System', 'Administrator', '+1-555-0100', 'REDY-MED Technology', 'Shenzhen', 'China');

-- Insert Sample Client User
-- Username: client
-- Password: client123
-- Password Hash generated using PHP: password_hash('client123', PASSWORD_DEFAULT)
INSERT INTO user_credentials (username, email, password_hash, role, is_active) VALUES
('client', 'client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', TRUE);

-- Insert Client Details
INSERT INTO user_details (user_id, first_name, last_name, phone, company_name, city, country) VALUES
(2, 'John', 'Doe', '+1-555-0200', 'Medical Supplies Inc.', 'New York', 'USA');

-- Insert Another Sample Client
-- Username: testuser
-- Password: test123
INSERT INTO user_credentials (username, email, password_hash, role, is_active) VALUES
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', TRUE);

-- Insert Test User Details
INSERT INTO user_details (user_id, first_name, last_name, phone, company_name, city, country) VALUES
(3, 'Jane', 'Smith', '+1-555-0300', 'Healthcare Solutions LLC', 'Los Angeles', 'USA');

-- ============================================
-- SAMPLE PRODUCT DATA
-- ============================================

-- Insert Sample Categories (with duplicate check)
INSERT IGNORE INTO categories (category_name, description) VALUES
('ECG Cables', 'Electrocardiogram cables and accessories for patient monitoring'),
('SPO2 Sensors', 'Pulse oximetry sensors for oxygen saturation monitoring'),
('IBP Cables', 'Invasive Blood Pressure monitoring cables'),
('NIBP Cuffs', 'Non-Invasive Blood Pressure cuffs and accessories'),
('Temperature Probes', 'Temperature monitoring probes and sensors');

-- Insert Sample Products
INSERT INTO products (product_name, sku, category_id, short_description, long_description, price, stock_quantity, is_active) VALUES
(
    '5-Lead ECG Cable - Snap Type',
    'ECG-5L-SNAP-001',
    1,
    'Universal 5-lead ECG cable with snap connectors for patient monitoring',
    'The REDY-MED 5-Lead ECG Cable features snap-type connectors for quick and secure electrode attachment. Compatible with major patient monitor brands including Philips, GE, and Mindray. Medical-grade materials ensure durability and patient safety. Latex-free construction suitable for sensitive patients.\n\nKey Features:\n- 5-lead configuration with color-coded wires\n- Snap-type connectors for easy electrode attachment\n- 3-meter cable length for flexibility\n- Universal compatibility with major monitor brands\n- Latex-free, DEHP-free materials\n- CE and ISO 13485 certified',
    89.99,
    150,
    TRUE
),
(
    'Adult SpO2 Finger Sensor - Reusable',
    'SPO2-ADT-REUSE-001',
    2,
    'Reusable adult finger SpO2 sensor with 3-meter cable',
    'High-quality reusable SpO2 sensor designed for adult patients. Features a durable clip design with soft padding for patient comfort during extended monitoring. Compatible with major patient monitors and pulse oximeters.\n\nKey Features:\n- Reusable design for cost-effectiveness\n- Soft padding for patient comfort\n- 3-meter cable length\n- Compatible with Philips, GE, Mindray, Masimo\n- Accurate readings even with low perfusion\n- Easy to clean and disinfect\n- 12-month warranty',
    45.99,
    200,
    TRUE
);

-- Add features column to products table if not exists
ALTER TABLE products ADD COLUMN IF NOT EXISTS features TEXT AFTER long_description;

-- Update products with features
UPDATE products SET features =
'High-quality materials for durability
Snap-type connectors for easy use
Universal compatibility with major brands
Latex-free construction
Medical-grade quality with certifications
3-meter cable length for flexibility'
WHERE sku = 'ECG-5L-SNAP-001';

UPDATE products SET features =
'Reusable design saves costs
Soft padding for comfort
Accurate readings with low perfusion
Easy to clean and disinfect
Compatible with major brands
12-month warranty included'
WHERE sku = 'SPO2-ADT-REUSE-001';

-- Insert Product Images
INSERT INTO product_images (product_id, image_url, image_alt_text, display_order, is_primary) VALUES
(1, 'https://via.placeholder.com/500x500/ffffff/00e600?text=ECG+Cable+5-Lead', '5-Lead ECG Cable Main Image', 0, TRUE),
(1, 'https://via.placeholder.com/500x500/ffffff/00b300?text=ECG+Detail+View', '5-Lead ECG Cable Detail', 1, FALSE),
(1, 'https://via.placeholder.com/500x500/ffffff/009900?text=ECG+Connectors', '5-Lead ECG Cable Connectors', 2, FALSE),
(2, 'https://via.placeholder.com/500x500/ffffff/00e600?text=SpO2+Sensor', 'Adult SpO2 Sensor Main Image', 0, TRUE),
(2, 'https://via.placeholder.com/500x500/ffffff/00b300?text=SpO2+Detail', 'Adult SpO2 Sensor Detail', 1, FALSE);

-- Insert Specification Attributes
INSERT INTO specification_attributes (attribute_name, data_type, unit, category_id, display_order) VALUES
('Cable Length', 'text', 'meters', 1, 1),
('Cable Color', 'text', NULL, 1, 2),
('Connector Type', 'text', NULL, 1, 3),
('Number of Leads', 'number', NULL, 1, 4),
('Material', 'text', NULL, 1, 5),
('Latex-Free', 'text', NULL, 1, 6),
('Warranty', 'text', 'months', 1, 7),
('Sensor Type', 'text', NULL, 2, 1),
('Patient Type', 'text', NULL, 2, 2),
('Cable Length', 'text', 'meters', 2, 3),
('Reusable', 'text', NULL, 2, 4),
('Warranty', 'text', 'months', 2, 5);

-- Insert Product Specifications for ECG Cable
INSERT INTO product_specifications (product_id, attribute_id, spec_value) VALUES
(1, 1, '3.0'),
(1, 2, 'Multi-color (color-coded)'),
(1, 3, 'Snap Type'),
(1, 4, '5'),
(1, 5, 'Medical Grade TPU'),
(1, 6, 'Yes'),
(1, 7, '12');

-- Insert Product Specifications for SpO2 Sensor
INSERT INTO product_specifications (product_id, attribute_id, spec_value) VALUES
(2, 8, 'Finger Clip'),
(2, 9, 'Adult'),
(2, 10, '3.0'),
(2, 11, 'Yes'),
(2, 12, '12');

-- Insert Manufacturers (with duplicate check)
INSERT IGNORE INTO manufacturers (manufacturer_name) VALUES
('Philips Healthcare'),
('GE Healthcare'),
('Mindray'),
('Spacelabs Healthcare'),
('Masimo');

-- Insert Device Models
INSERT INTO device_models (manufacturer_id, model_name) VALUES
(1, 'IntelliVue MP Series'),
(1, 'IntelliVue MX Series'),
(1, 'SureSigns VM Series'),
(2, 'DASH Series'),
(2, 'Solar Series'),
(2, 'Tram Series'),
(3, 'BeneView Series'),
(3, 'uMEC Series'),
(3, 'DPM Series');

-- Insert Product Compatibility for ECG Cable
INSERT INTO product_compatibility (product_id, model_id, notes) VALUES
(1, 1, 'Fully compatible'),
(1, 2, 'Fully compatible'),
(1, 3, 'Fully compatible'),
(1, 4, 'Fully compatible'),
(1, 5, 'Fully compatible'),
(1, 7, 'Fully compatible'),
(1, 8, 'Fully compatible');

-- Insert Product Compatibility for SpO2 Sensor
INSERT INTO product_compatibility (product_id, model_id, notes) VALUES
(2, 1, 'Compatible with SpO2 module'),
(2, 2, 'Compatible with SpO2 module'),
(2, 4, 'Compatible with SpO2 module'),
(2, 7, 'Compatible with SpO2 module');

-- Insert Certifications (with duplicate check)
INSERT IGNORE INTO certifications (certification_name, description) VALUES
('CE Mark', 'European Conformity certification for medical devices'),
('ISO 13485', 'Medical Device Quality Management System'),
('FDA 510(k)', 'FDA clearance for medical devices');

-- Insert Product Certifications
INSERT INTO product_certifications (product_id, certification_id, certificate_number, issue_date, expiry_date) VALUES
(1, 1, 'CE-ECG-2024-001', '2024-01-15', '2029-01-15'),
(1, 2, 'ISO-ECG-2024-001', '2024-01-20', NULL),
(2, 1, 'CE-SPO2-2024-002', '2024-02-10', '2029-02-10'),
(2, 2, 'ISO-SPO2-2024-002', '2024-02-15', NULL);

-- ============================================
-- NOTES FOR PASSWORD HASHING
-- ============================================
-- The password hashes in this file are generated using PHP's password_hash() function
-- To generate a new password hash, use: password_hash('your_password', PASSWORD_DEFAULT)
-- To verify a password, use: password_verify('input_password', $stored_hash)
--
-- Sample Credentials:
-- Admin: username=admin, password=admin123
-- Client: username=client, password=client123
-- Test User: username=testuser, password=test123
--
-- ============================================
-- HOW TO USE THIS FILE
-- ============================================
-- 1. Make sure you have already created the main database tables using ecomerce.sql
-- 2. Import this file into your ecomercedb database
-- 3. This will create the user tables and insert sample data
-- 4. You can then login with the credentials above

