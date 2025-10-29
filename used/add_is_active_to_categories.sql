-- Add is_active column to categories table
ALTER TABLE categories 
ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER description;

-- Update existing categories to be active
UPDATE categories SET is_active = 1 WHERE is_active IS NULL;

