-- Hero Slides Table for Home Page
CREATE TABLE IF NOT EXISTS hero_slides (
    slide_id INT PRIMARY KEY AUTO_INCREMENT,
    slide_order INT NOT NULL DEFAULT 1,
    small_title VARCHAR(100) NOT NULL,
    main_title VARCHAR(255) NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default slides
INSERT INTO hero_slides (slide_order, small_title, main_title, image_path, is_active) VALUES
(1, 'Medical Equipment', 'Professional Stethoscopes For Healthcare', 'images/hero/slide-1.jpg', 1),
(2, 'Surgical Supplies', 'Premium Medical Instruments & Tools', 'images/hero/slide-2.jpg', 1),
(3, 'Patient Care', 'Complete Healthcare Solutions & Support', 'images/hero/slide-3.jpg', 1);

