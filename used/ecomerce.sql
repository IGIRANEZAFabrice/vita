CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    parent_category_id INT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id)
);

CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    spu VARCHAR(100),
    brand VARCHAR(100) DEFAULT 'REDY-MED',
    category_id INT NOT NULL,
    short_description TEXT,
    long_description TEXT,
    price DECIMAL(10, 2),
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    INDEX idx_sku (sku),
    INDEX idx_brand (brand),
    INDEX idx_category (category_id)
);

-- Product Images Table
CREATE TABLE product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    image_alt_text VARCHAR(255),
    display_order INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
);

-- Additional Product Images Table (for gallery/additional photos)
CREATE TABLE product_additional_images (
    additional_image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    image_title VARCHAR(255),
    image_description TEXT,
    image_type ENUM('gallery', 'detail', 'lifestyle', 'technical', 'packaging', 'certification') DEFAULT 'gallery',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_image_type (image_type)
);

-- Specification Attributes Table (defines types of specs)
CREATE TABLE specification_attributes (
    attribute_id INT PRIMARY KEY AUTO_INCREMENT,
    attribute_name VARCHAR(100) NOT NULL,
    data_type ENUM('text', 'number', 'boolean', 'list') DEFAULT 'text',
    unit VARCHAR(50), -- e.g., 'mm', 'kg', 'V', 'mAh'
    category_id INT, -- NULL means applies to all categories
    display_order INT DEFAULT 0,
    is_filterable BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    INDEX idx_category (category_id)
);

-- Product Specifications Table (actual values)
CREATE TABLE product_specifications (
    spec_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    attribute_id INT NOT NULL,
    spec_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES specification_attributes(attribute_id),
    UNIQUE KEY unique_product_attribute (product_id, attribute_id),
    INDEX idx_product (product_id),
    INDEX idx_attribute (attribute_id)
);

-- Manufacturers Table
CREATE TABLE manufacturers (
    manufacturer_id INT PRIMARY KEY AUTO_INCREMENT,
    manufacturer_name VARCHAR(200) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Device Models Table
CREATE TABLE device_models (
    model_id INT PRIMARY KEY AUTO_INCREMENT,
    manufacturer_id INT NOT NULL,
    model_name VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(manufacturer_id),
    UNIQUE KEY unique_manufacturer_model (manufacturer_id, model_name),
    INDEX idx_manufacturer (manufacturer_id)
);

-- Product Compatibility Table
CREATE TABLE product_compatibility (
    compatibility_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    model_id INT NOT NULL,
    notes TEXT, -- Any specific compatibility notes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES device_models(model_id),
    UNIQUE KEY unique_product_model (product_id, model_id),
    INDEX idx_product (product_id),
    INDEX idx_model (model_id)
);

-- Product Variants Table (for different product numbers/colors/sizes)
CREATE TABLE product_variants (
    variant_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    variant_sku VARCHAR(100) UNIQUE NOT NULL,
    variant_name VARCHAR(200),
    variant_type VARCHAR(50), -- e.g., 'connector_type', 'color', 'size'
    variant_value VARCHAR(100), -- e.g., 'Utah', 'Blue', 'Large'
    price_adjustment DECIMAL(10, 2) DEFAULT 0.00,
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_sku (variant_sku)
);

-- Certifications Table
CREATE TABLE certifications (
    certification_id INT PRIMARY KEY AUTO_INCREMENT,
    certification_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product Certifications Table
CREATE TABLE product_certifications (
    product_certification_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    certification_id INT NOT NULL,
    certificate_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (certification_id) REFERENCES certifications(certification_id),
    UNIQUE KEY unique_product_certification (product_id, certification_id),
    INDEX idx_product (product_id)
);

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert Categories
INSERT INTO categories (category_name, description) VALUES
('Cables', 'Medical cables and connectors'),
('ECG Cables', 'Electrocardiogram cables'),
('IBP Cables', 'Invasive Blood Pressure cables'),
('SpO2 Cables', 'Pulse Oximetry cables'),
('Batteries', 'Medical device batteries'),
('Accessories', 'Medical device accessories'),
('Veterinary Products', 'Veterinary medical equipment');

-- Insert Specification Attributes for Cables
INSERT INTO specification_attributes (attribute_name, data_type, unit, category_id, display_order, is_filterable) VALUES
-- Cable specifications
('Cable Color', 'text', NULL, 1, 1, TRUE),
('Cable Diameter', 'text', 'mm', 1, 2, FALSE),
('Total Cable Length', 'text', 'm', 1, 3, TRUE),
('Trunk Cable Material', 'text', NULL, 1, 4, FALSE),
('Trunk Cable Color', 'text', NULL, 1, 5, FALSE),
('Connector Distal', 'text', NULL, 1, 6, TRUE),
('Connector Proximal', 'text', NULL, 1, 7, TRUE),
('Weight', 'text', 'kg', NULL, 8, FALSE),
('Latex-free', 'text', NULL, NULL, 9, TRUE),
('Sterile', 'text', NULL, NULL, 10, TRUE),
('Packaging Type', 'text', NULL, NULL, 11, FALSE),
('Packaging Unit', 'text', NULL, NULL, 12, FALSE),
('Warranty', 'text', NULL, NULL, 13, FALSE),
('Lead Number', 'text', NULL, 2, 14, TRUE),
('Patient Size', 'text', NULL, NULL, 15, TRUE);

-- Battery specifications
INSERT INTO specification_attributes (attribute_name, data_type, unit, category_id, display_order, is_filterable) VALUES
('Color', 'text', NULL, 5, 1, TRUE),
('Batteries Core Quantity', 'text', 'cell', 5, 2, FALSE),
('Batteries Core Capacity', 'text', NULL, 5, 3, FALSE),
('Cell Type', 'text', NULL, 5, 4, TRUE),
('Batteries Core Brand', 'text', NULL, 5, 5, TRUE),
('Volt', 'text', 'V', 5, 6, TRUE),
('Capacity', 'text', 'mAh', 5, 7, TRUE),
('Size', 'text', 'mm', 5, 8, FALSE);

-- Insert Certifications
INSERT INTO certifications (certification_name, description) VALUES
('ISO13485', 'Medical devices quality management'),
('CE', 'European Conformity'),
('FDA', 'Food and Drug Administration approval');

-- Insert Manufacturers (Sample from your data)
INSERT INTO manufacturers (manufacturer_name) VALUES
('AAMI'),
('ATL'),
('Abbott'),
('GE Healthcare'),
('Philips'),
('Mindray'),
('Nihon Kohden'),
('Spacelabs'),
('Welch Allyn'),
('Zoll'),
('Draeger'),
('Edan'),
('Medtronic'),
('Covidien'),
('Nellcor'),
('BCI'),
('Smiths Medical'),
('Biocare');
