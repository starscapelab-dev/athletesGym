-- Add missing columns to products table for stock management and featured products

ALTER TABLE `products` ADD COLUMN `track_stock` TINYINT(1) DEFAULT 1 AFTER `price`;
ALTER TABLE `products` ADD COLUMN `low_stock_threshold` INT DEFAULT 5 AFTER `track_stock`;
ALTER TABLE `products` ADD COLUMN `featured` TINYINT(1) DEFAULT 0 AFTER `low_stock_threshold`;
