-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 02:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecomercedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `parent_category_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Cables', NULL, 'Medical cables and connectors', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(2, 'ECG Cables', NULL, 'Electrocardiogram cables', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(3, 'IBP Cables', NULL, 'Invasive Blood Pressure cables', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(4, 'SpO2 Cables', NULL, 'Pulse Oximetry cables', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(5, 'Batteries', NULL, 'Medical device batteries', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(6, 'Accessories', NULL, 'Medical device accessories', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(7, 'Veterinary Products', NULL, 'Veterinary medical equipment', '2025-10-28 20:33:16', '2025-10-28 20:33:16'),
(8, 'ECG Cables', NULL, 'Electrocardiogram cables and accessories for patient monitoring', '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(9, 'SPO2 Sensors', NULL, 'Pulse oximetry sensors for oxygen saturation monitoring', '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(10, 'IBP Cables', NULL, 'Invasive Blood Pressure monitoring cables', '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(11, 'NIBP Cuffs', NULL, 'Non-Invasive Blood Pressure cuffs and accessories', '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(12, 'Temperature Probes', NULL, 'Temperature monitoring probes and sensors', '2025-10-28 20:33:17', '2025-10-28 20:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `certification_id` int(11) NOT NULL,
  `certification_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`certification_id`, `certification_name`, `description`, `created_at`) VALUES
(1, 'ISO13485', 'Medical devices quality management', '2025-10-28 20:33:16'),
(2, 'CE', 'European Conformity', '2025-10-28 20:33:16'),
(3, 'FDA', 'Food and Drug Administration approval', '2025-10-28 20:33:16'),
(4, 'CE Mark', 'European Conformity certification for medical devices', '2025-10-28 20:33:18'),
(5, 'ISO 13485', 'Medical Device Quality Management System', '2025-10-28 20:33:18'),
(6, 'FDA 510(k)', 'FDA clearance for medical devices', '2025-10-28 20:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `device_models`
--

CREATE TABLE `device_models` (
  `model_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `model_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device_models`
--

INSERT INTO `device_models` (`model_id`, `manufacturer_id`, `model_name`, `created_at`) VALUES
(1, 1, 'IntelliVue MP Series', '2025-10-28 20:33:17'),
(2, 1, 'IntelliVue MX Series', '2025-10-28 20:33:17'),
(3, 1, 'SureSigns VM Series', '2025-10-28 20:33:17'),
(4, 2, 'DASH Series', '2025-10-28 20:33:17'),
(5, 2, 'Solar Series', '2025-10-28 20:33:17'),
(6, 2, 'Tram Series', '2025-10-28 20:33:17'),
(7, 3, 'BeneView Series', '2025-10-28 20:33:17'),
(8, 3, 'uMEC Series', '2025-10-28 20:33:17'),
(9, 3, 'DPM Series', '2025-10-28 20:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `slide_id` int(11) NOT NULL,
  `slide_order` int(11) NOT NULL DEFAULT 1,
  `small_title` varchar(100) NOT NULL,
  `main_title` varchar(255) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`slide_id`, `slide_order`, `small_title`, `main_title`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Medical Equipment', 'Professional Stethoscopes For Healthcare', 'images/hero/slide-1-1761692596.png', 1, '2025-10-28 22:51:39', '2025-10-28 23:03:16'),
(2, 2, 'Surgical Supplies', 'Premium Medical Instruments & Tools', 'images/hero/slide-2.jpg', 1, '2025-10-28 22:51:39', '2025-10-28 22:51:39'),
(3, 3, 'Patient Care', 'Complete Healthcare Solutions & Support', 'images/hero/slide-3-1761692587.png', 1, '2025-10-28 22:51:39', '2025-10-28 23:03:07');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

CREATE TABLE `manufacturers` (
  `manufacturer_id` int(11) NOT NULL,
  `manufacturer_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `manufacturers` (`manufacturer_id`, `manufacturer_name`, `created_at`) VALUES
(1, 'AAMI', '2025-10-28 20:33:16'),
(2, 'ATL', '2025-10-28 20:33:16'),
(3, 'Abbott', '2025-10-28 20:33:16'),
(4, 'GE Healthcare', '2025-10-28 20:33:16'),
(5, 'Philips', '2025-10-28 20:33:16'),
(6, 'Mindray', '2025-10-28 20:33:16'),
(7, 'Nihon Kohden', '2025-10-28 20:33:16'),
(8, 'Spacelabs', '2025-10-28 20:33:16'),
(9, 'Welch Allyn', '2025-10-28 20:33:16'),
(10, 'Zoll', '2025-10-28 20:33:16'),
(11, 'Draeger', '2025-10-28 20:33:16'),
(12, 'Edan', '2025-10-28 20:33:16'),
(13, 'Medtronic', '2025-10-28 20:33:16'),
(14, 'Covidien', '2025-10-28 20:33:16'),
(15, 'Nellcor', '2025-10-28 20:33:16'),
(16, 'BCI', '2025-10-28 20:33:16'),
(17, 'Smiths Medical', '2025-10-28 20:33:16'),
(18, 'Biocare', '2025-10-28 20:33:16'),
(19, 'Philips Healthcare', '2025-10-28 20:33:17'),
(20, 'Spacelabs Healthcare', '2025-10-28 20:33:17'),
(21, 'Masimo', '2025-10-28 20:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `spu` varchar(100) DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `warranty_period` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT 'REDY-MED',
  `category_id` int(11) NOT NULL,
  `manufacturer_id` int(11) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `sku`, `spu`, `model_number`, `warranty_period`, `brand`, `category_id`, `manufacturer_id`, `short_description`, `long_description`, `description`, `features`, `price`, `stock_quantity`, `is_active`, `is_featured`, `created_at`, `updated_at`) VALUES
(9, 'cable test', '1', '1', '1', '1', 'test', 1, 10, 'Product Overview\r\nHigh-quality reusable SpO2 sensor designed for adult patients. Features a durable clip design with soft padding for patient comfort during extended monitoring. Compatible with major patient monitors and pulse oximeters.\r\n\r\nKey Features:\r\n- Reusable design for cost-effectiveness\r\n- Soft padding for patient comfort\r\n- 3-meter cable length\r\n- Compatible with Philips, GE, Mindray, Masimo\r\n- Accurate readings even with low perfusion\r\n- Easy to clean and disinfect\r\n- 12-month warranty\r\n\r\nKey Features\r\nReusable design saves costs\r\nSoft padding for comfort\r\nAccurate readings with low perfusion\r\nEasy to clean and disinfect\r\nCompatible with major brands\r\n12-month warranty included', NULL, NULL, NULL, 1.00, 1, 1, 0, '2025-10-29 00:21:14', '2025-10-29 00:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_additional_images`
--

CREATE TABLE `product_additional_images` (
  `additional_image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `image_title` varchar(255) DEFAULT NULL,
  `image_description` text DEFAULT NULL,
  `image_type` enum('gallery','detail','lifestyle','technical','packaging','certification') DEFAULT 'gallery',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_certifications`
--

CREATE TABLE `product_certifications` (
  `product_certification_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `certification_id` int(11) NOT NULL,
  `certificate_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_certifications`
--

INSERT INTO `product_certifications` (`product_certification_id`, `product_id`, `certification_id`, `certificate_number`, `issue_date`, `expiry_date`, `created_at`) VALUES
(11, 9, 2, NULL, NULL, NULL, '2025-10-29 00:21:14'),
(12, 9, 5, NULL, NULL, NULL, '2025-10-29 00:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_compatibility`
--

CREATE TABLE `product_compatibility` (
  `compatibility_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_compatibility`
--

INSERT INTO `product_compatibility` (`compatibility_id`, `product_id`, `model_id`, `notes`, `created_at`) VALUES
(24, 9, 1, NULL, '2025-10-29 00:21:14'),
(25, 9, 3, NULL, '2025-10-29 00:21:14'),
(26, 9, 7, NULL, '2025-10-29 00:21:14'),
(27, 9, 8, NULL, '2025-10-29 00:21:14'),
(28, 9, 4, NULL, '2025-10-29 00:21:14'),
(29, 9, 5, NULL, '2025-10-29 00:21:14'),
(30, 9, 6, NULL, '2025-10-29 00:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `image_alt_text` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `image_alt_text`, `display_order`, `is_primary`, `created_at`) VALUES
(15, 9, 'images/products/product_9_main_69015dfa9e24b_1761697274.jpg', NULL, 0, 1, '2025-10-29 00:21:14'),
(16, 9, 'images/products/product_9_gallery_69015dfaa0164_1761697274.jpg', NULL, 0, 0, '2025-10-29 00:21:14'),
(17, 9, 'images/products/product_9_gallery_69015dfaa1849_1761697274.jpg', NULL, 0, 0, '2025-10-29 00:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

CREATE TABLE `product_specifications` (
  `spec_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `spec_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_specifications`
--

INSERT INTO `product_specifications` (`spec_id`, `product_id`, `attribute_id`, `spec_value`, `created_at`, `updated_at`) VALUES
(41, 9, 1, 'red', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(42, 9, 24, '120', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(43, 9, 25, 'red', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(44, 9, 2, '20', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(45, 9, 3, '120', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(46, 9, 26, 'Type C', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(47, 9, 4, 'remon', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(48, 9, 27, '100', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(49, 9, 28, 'cotton', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(50, 9, 5, 'yellow', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(51, 9, 6, 'wire', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(52, 9, 29, 'yes', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(53, 9, 30, '12', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(54, 9, 7, '21', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(55, 9, 8, '23', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(56, 9, 9, '3', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(57, 9, 10, 'no', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(58, 9, 11, 'bag', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(59, 9, 12, 'meter', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(60, 9, 13, '1', '2025-10-29 00:21:14', '2025-10-29 00:21:14'),
(61, 9, 15, '3', '2025-10-29 00:21:14', '2025-10-29 00:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_sku` varchar(100) NOT NULL,
  `variant_name` varchar(200) DEFAULT NULL,
  `variant_type` varchar(50) DEFAULT NULL,
  `variant_value` varchar(100) DEFAULT NULL,
  `price_adjustment` decimal(10,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `specification_attributes`
--

CREATE TABLE `specification_attributes` (
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `data_type` enum('text','number','boolean','list') DEFAULT 'text',
  `unit` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_filterable` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specification_attributes`
--

INSERT INTO `specification_attributes` (`attribute_id`, `attribute_name`, `data_type`, `unit`, `category_id`, `display_order`, `is_filterable`, `created_at`) VALUES
(1, 'Cable Color', 'text', NULL, 1, 1, 1, '2025-10-28 20:33:16'),
(2, 'Cable Diameter', 'text', 'mm', 1, 2, 0, '2025-10-28 20:33:16'),
(3, 'Total Cable Length', 'text', 'm', 1, 3, 1, '2025-10-28 20:33:16'),
(4, 'Trunk Cable Material', 'text', NULL, 1, 4, 0, '2025-10-28 20:33:16'),
(5, 'Trunk Cable Color', 'text', NULL, 1, 5, 0, '2025-10-28 20:33:16'),
(6, 'Connector Distal', 'text', NULL, 1, 6, 1, '2025-10-28 20:33:16'),
(7, 'Connector Proximal', 'text', NULL, 1, 7, 1, '2025-10-28 20:33:16'),
(8, 'Weight', 'text', 'kg', NULL, 8, 0, '2025-10-28 20:33:16'),
(9, 'Latex-free', 'text', NULL, NULL, 9, 1, '2025-10-28 20:33:16'),
(10, 'Sterile', 'text', NULL, NULL, 10, 1, '2025-10-28 20:33:16'),
(11, 'Packaging Type', 'text', NULL, NULL, 11, 0, '2025-10-28 20:33:16'),
(12, 'Packaging Unit', 'text', NULL, NULL, 12, 0, '2025-10-28 20:33:16'),
(13, 'Warranty', 'text', NULL, NULL, 13, 0, '2025-10-28 20:33:16'),
(14, 'Lead Number', 'text', NULL, 2, 14, 1, '2025-10-28 20:33:16'),
(15, 'Patient Size', 'text', NULL, NULL, 15, 1, '2025-10-28 20:33:16'),
(16, 'Color', 'text', NULL, 5, 1, 1, '2025-10-28 20:33:16'),
(17, 'Batteries Core Quantity', 'text', 'cell', 5, 2, 0, '2025-10-28 20:33:16'),
(18, 'Batteries Core Capacity', 'text', NULL, 5, 3, 0, '2025-10-28 20:33:16'),
(19, 'Cell Type', 'text', NULL, 5, 4, 1, '2025-10-28 20:33:16'),
(20, 'Batteries Core Brand', 'text', NULL, 5, 5, 1, '2025-10-28 20:33:16'),
(21, 'Volt', 'text', 'V', 5, 6, 1, '2025-10-28 20:33:16'),
(22, 'Capacity', 'text', 'mAh', 5, 7, 1, '2025-10-28 20:33:16'),
(23, 'Size', 'text', 'mm', 5, 8, 0, '2025-10-28 20:33:16'),
(24, 'Cable Length', 'text', 'meters', 1, 1, 0, '2025-10-28 20:33:17'),
(25, 'Cable Color', 'text', NULL, 1, 2, 0, '2025-10-28 20:33:17'),
(26, 'Connector Type', 'text', NULL, 1, 3, 0, '2025-10-28 20:33:17'),
(27, 'Number of Leads', 'number', NULL, 1, 4, 0, '2025-10-28 20:33:17'),
(28, 'Material', 'text', NULL, 1, 5, 0, '2025-10-28 20:33:17'),
(29, 'Latex-Free', 'text', NULL, 1, 6, 0, '2025-10-28 20:33:17'),
(30, 'Warranty', 'text', 'months', 1, 7, 0, '2025-10-28 20:33:17'),
(31, 'Sensor Type', 'text', NULL, 2, 1, 0, '2025-10-28 20:33:17'),
(32, 'Patient Type', 'text', NULL, 2, 2, 0, '2025-10-28 20:33:17'),
(33, 'Cable Length', 'text', 'meters', 2, 3, 0, '2025-10-28 20:33:17'),
(34, 'Reusable', 'text', NULL, 2, 4, 0, '2025-10-28 20:33:17'),
(35, 'Warranty', 'text', 'months', 2, 5, 0, '2025-10-28 20:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_credentials`
--

CREATE TABLE `user_credentials` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL DEFAULT 'client',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_credentials`
--

INSERT INTO `user_credentials` (`user_id`, `username`, `email`, `password_hash`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'test@gmail.com', '$2y$10$zoVXk7D7q2sMSfkSCdajG.H4yLgiaP2hT4bte47m0FalLmJKQvsGO', 'admin', 1, '2025-10-29 01:00:49', '2025-10-28 20:33:17', '2025-10-29 01:00:49'),
(2, 'client', 'client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 1, NULL, '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(3, 'testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 1, NULL, '2025-10-28 20:33:17', '2025-10-28 20:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `detail_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state_province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'USA',
  `profile_image` varchar(500) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`detail_id`, `user_id`, `first_name`, `last_name`, `phone`, `company_name`, `address_line1`, `address_line2`, `city`, `state_province`, `postal_code`, `country`, `profile_image`, `bio`, `created_at`, `updated_at`) VALUES
(1, 1, 'IGIRANEZA', 'Fabrice', '+1-555-0100', 'Test', '', '', 'kigali', '', '', 'rwanda', 'images/profiles/profile-1-1761693375.png', 'none', '2025-10-28 20:33:17', '2025-10-28 23:16:15'),
(2, 2, 'John', 'Doe', '+1-555-0200', 'Medical Supplies Inc.', NULL, NULL, 'New York', NULL, NULL, 'USA', NULL, NULL, '2025-10-28 20:33:17', '2025-10-28 20:33:17'),
(3, 3, 'Jane', 'Smith', '+1-555-0300', 'Healthcare Solutions LLC', NULL, NULL, 'Los Angeles', NULL, NULL, 'USA', NULL, NULL, '2025-10-28 20:33:17', '2025-10-28 20:33:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`certification_id`),
  ADD UNIQUE KEY `certification_name` (`certification_name`);

--
-- Indexes for table `device_models`
--
ALTER TABLE `device_models`
  ADD PRIMARY KEY (`model_id`),
  ADD UNIQUE KEY `unique_manufacturer_model` (`manufacturer_id`,`model_name`),
  ADD KEY `idx_manufacturer` (`manufacturer_id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`slide_id`);

--
-- Indexes for table `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`manufacturer_id`),
  ADD UNIQUE KEY `manufacturer_name` (`manufacturer_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_sku` (`sku`),
  ADD KEY `idx_brand` (`brand`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `product_additional_images`
--
ALTER TABLE `product_additional_images`
  ADD PRIMARY KEY (`additional_image_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_image_type` (`image_type`);

--
-- Indexes for table `product_certifications`
--
ALTER TABLE `product_certifications`
  ADD PRIMARY KEY (`product_certification_id`),
  ADD UNIQUE KEY `unique_product_certification` (`product_id`,`certification_id`),
  ADD KEY `certification_id` (`certification_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  ADD PRIMARY KEY (`compatibility_id`),
  ADD UNIQUE KEY `unique_product_model` (`product_id`,`model_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_model` (`model_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD PRIMARY KEY (`spec_id`),
  ADD UNIQUE KEY `unique_product_attribute` (`product_id`,`attribute_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_attribute` (`attribute_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD UNIQUE KEY `variant_sku` (`variant_sku`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_sku` (`variant_sku`);

--
-- Indexes for table `specification_attributes`
--
ALTER TABLE `specification_attributes`
  ADD PRIMARY KEY (`attribute_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `user_credentials`
--
ALTER TABLE `user_credentials`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `certification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `device_models`
--
ALTER TABLE `device_models`
  MODIFY `model_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `slide_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_additional_images`
--
ALTER TABLE `product_additional_images`
  MODIFY `additional_image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_certifications`
--
ALTER TABLE `product_certifications`
  MODIFY `product_certification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  MODIFY `compatibility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_specifications`
--
ALTER TABLE `product_specifications`
  MODIFY `spec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specification_attributes`
--
ALTER TABLE `specification_attributes`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user_credentials`
--
ALTER TABLE `user_credentials`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `device_models`
--
ALTER TABLE `device_models`
  ADD CONSTRAINT `device_models_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`manufacturer_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_additional_images`
--
ALTER TABLE `product_additional_images`
  ADD CONSTRAINT `product_additional_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_certifications`
--
ALTER TABLE `product_certifications`
  ADD CONSTRAINT `product_certifications_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_certifications_ibfk_2` FOREIGN KEY (`certification_id`) REFERENCES `certifications` (`certification_id`);

--
-- Constraints for table `product_compatibility`
--
ALTER TABLE `product_compatibility`
  ADD CONSTRAINT `product_compatibility_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_compatibility_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `device_models` (`model_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD CONSTRAINT `product_specifications_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_specifications_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `specification_attributes` (`attribute_id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `specification_attributes`
--
ALTER TABLE `specification_attributes`
  ADD CONSTRAINT `specification_attributes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_credentials` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
