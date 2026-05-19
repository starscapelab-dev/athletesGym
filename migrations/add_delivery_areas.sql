-- Create delivery_areas table
CREATE TABLE IF NOT EXISTS `delivery_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `shipping_charge` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert delivery areas with charges
INSERT INTO `delivery_areas` (`name`, `shipping_charge`, `is_active`) VALUES
('Inside Doha', 25.00, 1),
('Umm Salal', 30.00, 1),
('Al Wakrah and Wukair', 40.00, 1),
('Alkhor', 50.00, 1),
('Al Shamal', 100.00, 1);

-- Add delivery_area_id column to orders table if it doesn't exist
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `delivery_area_id` INT(11) DEFAULT NULL AFTER `city`;

-- Add foreign key constraint
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_delivery_area`
FOREIGN KEY (`delivery_area_id`) REFERENCES `delivery_areas` (`id`) ON DELETE SET NULL;
