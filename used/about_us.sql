-- ============================================================================
-- About Us Page Content Table - SQL Script
-- ============================================================================
-- Description: This script creates the about_us_content table and populates
--              it with default content for the About Us page.
-- Author: REDY-MED Development Team
-- Created: 2025-10-29
-- Version: 1.0
-- ============================================================================

-- ============================================================================
-- SECTION 1: DROP EXISTING TABLE (if exists)
-- ============================================================================
-- Warning: This will delete all existing data in the about_us_content table
-- Comment out this line if you want to preserve existing data

DROP TABLE IF EXISTS about_us_content;

-- ============================================================================
-- SECTION 2: CREATE TABLE
-- ============================================================================

CREATE TABLE about_us_content (
    -- Primary Key
    content_id INT(11) NOT NULL AUTO_INCREMENT,

    -- Section Identification
    section_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Unique identifier for the section (e.g., hero, mission, vision)',

    -- Content Fields
    section_title VARCHAR(255) NOT NULL COMMENT 'Display title of the section',
    section_content TEXT NOT NULL COMMENT 'Main content of the section (supports multiple paragraphs)',
    section_image VARCHAR(255) DEFAULT NULL COMMENT 'Optional image path for the section',

    -- Display Control
    section_order INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order of sections (lower numbers appear first)',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Whether the section is active (1) or hidden (0)',

    -- Timestamps
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the section was created',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When the section was last updated',

    -- Constraints
    PRIMARY KEY (content_id),
    UNIQUE KEY uk_section_name (section_name),
    KEY idx_section_order (section_order),
    KEY idx_is_active (is_active),
    KEY idx_created_at (created_at),
    KEY idx_updated_at (updated_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores content for the About Us page';

-- ============================================================================
-- SECTION 3: INSERT DEFAULT CONTENT
-- ============================================================================

-- Insert Hero Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'hero',
    'About REDY-MED',
    'REDY-MED is a leading provider of high-quality medical equipment and supplies. With years of experience in the healthcare industry, we are committed to delivering innovative solutions that improve patient care and support healthcare professionals worldwide.\n\nOur comprehensive range of products includes diagnostic equipment, surgical instruments, patient monitoring systems, and medical consumables. We partner with renowned manufacturers globally to ensure that every product meets the highest international standards of quality and safety.\n\nAt REDY-MED, we understand the critical role that reliable medical equipment plays in healthcare delivery. That\'s why we go beyond just supplying products – we provide complete solutions that include technical support, training, and after-sales service to ensure our clients can focus on what matters most: patient care.',
    'images/about/hero.jpg',
    1,
    1
);

-- Insert Mission Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'mission',
    'Our Mission',
    'Our mission is to provide healthcare facilities with reliable, cutting-edge medical equipment that enhances patient outcomes and streamlines clinical workflows. We strive to be a trusted partner in healthcare, offering exceptional products and unparalleled customer service.\n\nWe are dedicated to:\n• Sourcing and supplying only the highest quality medical equipment\n• Providing expert technical support and training to healthcare professionals\n• Ensuring timely delivery and efficient logistics\n• Building long-term relationships based on trust and reliability\n• Contributing to improved healthcare outcomes in our communities',
    'images/about/mission.jpg',
    2,
    1
);

-- Insert Vision Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'vision',
    'Our Vision',
    'We envision a world where every healthcare provider has access to the best medical technology, enabling them to deliver superior care to their patients. Through continuous innovation and dedication to excellence, we aim to be the global leader in medical equipment distribution.\n\nOur vision encompasses:\n• Expanding access to advanced medical technology across all healthcare settings\n• Pioneering new standards in medical equipment distribution and support\n• Fostering innovation through partnerships with leading manufacturers\n• Creating a sustainable healthcare ecosystem that benefits patients, providers, and communities\n• Setting the benchmark for quality, service, and reliability in the medical equipment industry',
    'images/about/vision.jpg',
    3,
    1
);

-- Insert Core Values Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'values',
    'Our Core Values',
    'Quality: We never compromise on the quality of our products. Every item in our catalog is carefully selected and rigorously tested to meet international standards.\nIntegrity: We conduct business with honesty, transparency, and ethical practices. Our clients trust us because we always do what we say we will do.\nInnovation: We embrace new technologies and solutions that advance healthcare. We continuously update our product range to include the latest medical innovations.\nCustomer Focus: Your success is our priority. We listen to your needs and provide tailored solutions that help you deliver better patient care.\nReliability: We deliver on our promises, every time. From product quality to delivery schedules, you can count on REDY-MED.\nExcellence: We strive for excellence in everything we do, from product selection to customer service, ensuring the highest standards at every touchpoint.',
    'images/about/values.jpg',
    4,
    1
);

-- Insert History Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'history',
    'Our History',
    'Founded in 2010, REDY-MED began as a small distributor of medical supplies with a vision to transform healthcare delivery. Over the years, we have grown into a comprehensive medical equipment provider, serving hospitals, clinics, and healthcare facilities across the region.\n\nOur journey has been marked by significant milestones:\n\n2010: REDY-MED was established with a focus on providing quality medical supplies to local healthcare facilities.\n\n2013: Expanded our product range to include advanced diagnostic equipment and formed partnerships with leading international manufacturers.\n\n2016: Opened our state-of-the-art distribution center and established a dedicated technical support team.\n\n2019: Achieved ISO 13485 certification for medical device quality management systems.\n\n2022: Launched our comprehensive training program for healthcare professionals and expanded our service coverage.\n\n2024: Celebrated serving over 500 healthcare facilities and introduced our digital platform for seamless ordering and support.\n\nThroughout our growth, our commitment to quality and customer satisfaction has remained the cornerstone of our success. We continue to evolve and adapt to meet the changing needs of the healthcare industry.',
    'images/about/history.jpg',
    5,
    1
);

-- Insert Team Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'team',
    'Our Team',
    'Our team consists of experienced healthcare professionals, biomedical engineers, and customer service specialists who are passionate about improving healthcare. With diverse backgrounds and expertise, we work together to ensure that our clients receive the best products and support in the industry.\n\nOur team includes:\n\n• Clinical Specialists: Healthcare professionals with hands-on experience who understand the practical needs of medical facilities.\n\n• Biomedical Engineers: Technical experts who provide installation, maintenance, and troubleshooting support for complex medical equipment.\n\n• Product Specialists: Knowledgeable professionals who help clients select the right equipment for their specific needs.\n\n• Customer Service Team: Dedicated support staff available to assist with orders, inquiries, and after-sales service.\n\n• Logistics Coordinators: Efficient team members who ensure timely delivery and proper handling of all medical equipment.\n\nTogether, we bring decades of combined experience in healthcare and medical equipment distribution, united by our commitment to excellence and customer satisfaction.',
    'images/about/team.jpg',
    6,
    1
);

-- Insert Why Choose Us Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'why_choose_us',
    'Why Choose REDY-MED?',
    'Extensive Product Range: From diagnostic equipment to surgical instruments, patient monitors to laboratory supplies – we offer a comprehensive catalog of over 2,000 medical products.\nCertified Quality: All products meet international standards including ISO, CE, and FDA certifications. We work only with reputable manufacturers.\nExpert Support: Our team of biomedical engineers and clinical specialists provide technical assistance, installation support, and comprehensive training.\nCompetitive Pricing: We offer the best value for your investment with transparent pricing and flexible payment options.\nFast Delivery: Efficient logistics network ensures timely delivery across the region, with emergency delivery options available.\nAfter-Sales Service: Comprehensive warranty coverage, maintenance support, and readily available spare parts ensure long-term reliability.\nCustomized Solutions: We work with you to understand your specific needs and provide tailored equipment packages.\nTraining Programs: Complimentary training sessions for your staff on equipment operation and maintenance.',
    'images/about/why-choose.jpg',
    7,
    1
);

-- Insert Certifications Section
INSERT INTO about_us_content (
    section_name,
    section_title,
    section_content,
    section_image,
    section_order,
    is_active
) VALUES (
    'certifications',
    'Certifications & Compliance',
    'REDY-MED is committed to maintaining the highest standards of quality and safety. Our products and operations are certified by leading international regulatory bodies, ensuring that you receive only the best medical equipment.\n\nOur Certifications Include:\n\n• ISO 13485:2016 - Medical Devices Quality Management System certification, demonstrating our commitment to consistent quality in medical device distribution.\n\n• CE Marking - European conformity marking for medical devices, ensuring compliance with EU health, safety, and environmental protection standards.\n\n• FDA Registration - Products distributed in the United States meet Food and Drug Administration requirements and regulations.\n\n• Good Distribution Practice (GDP) - Compliance with international guidelines for proper distribution of medical products.\n\n• ISO 9001:2015 - Quality Management System certification for overall business operations.\n\nQuality Assurance Process:\n\nWe regularly audit our suppliers and conduct rigorous quality control checks at every stage – from procurement to delivery. Our quality assurance team inspects all incoming products, maintains proper storage conditions, and ensures that all equipment is properly calibrated and tested before delivery.\n\nEvery product comes with complete documentation including certificates of conformity, user manuals, and warranty information. We maintain full traceability of all products for safety and regulatory compliance.',
    'images/about/certifications.jpg',
    8,
    1
);

-- ============================================================================
-- SECTION 4: VERIFICATION QUERIES
-- ============================================================================

-- Verify table creation
SELECT 'Table created successfully' AS status;

-- Count total records inserted
SELECT COUNT(*) AS total_sections FROM about_us_content;

-- Display all sections with their order
SELECT
    content_id,
    section_name,
    section_title,
    section_order,
    is_active,
    created_at
FROM about_us_content
ORDER BY section_order ASC;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================

