-- =====================================================
-- UPDATE PRODUCTS TABLE - ADD MISSING COLUMNS
-- =====================================================
-- This migration adds all missing columns to the products table
-- to match the schema defined in ecomerce.sql

-- 1. Add manufacturer_id column (if not exists)
ALTER TABLE products
ADD COLUMN manufacturer_id INT NULL AFTER category_id;

-- 2. Add model_number column (if not exists)
ALTER TABLE products
ADD COLUMN model_number VARCHAR(100) NULL AFTER spu;

-- 3. Add warranty_period column (if not exists)
ALTER TABLE products
ADD COLUMN warranty_period VARCHAR(100) NULL AFTER model_number;

-- 4. Add description column (general description - if not exists)
ALTER TABLE products
ADD COLUMN description TEXT AFTER long_description;

-- 5. Add is_featured column (if not exists)
ALTER TABLE products
ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER is_active;

-- =====================================================
-- UPDATE EXISTING DATA
-- =====================================================

-- Set default values for existing products
UPDATE products SET description = short_description WHERE description IS NULL;
UPDATE products SET is_featured = 0 WHERE is_featured IS NULL;
UPDATE products SET model_number = NULL WHERE model_number = '';
UPDATE products SET warranty_period = NULL WHERE warranty_period = '';

-- =====================================================
-- ADD FOREIGN KEY CONSTRAINTS (Optional)
-- =====================================================
-- Uncomment the line below if you want to add foreign key constraint
-- ALTER TABLE products ADD FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(manufacturer_id);

-- =====================================================
-- VERIFY THE SCHEMA
-- =====================================================
-- Run this to verify all columns exist:
-- DESCRIBE products;

-- Expected columns in products table:
-- product_id, product_name, sku, spu, brand, category_id, manufacturer_id,
-- model_number, warranty_period, short_description, long_description, description,
-- price, stock_quantity, is_active, is_featured, created_at, updated_at

