-- ========================================
-- Stock Management Migration
-- Athletes Gym - Product Stock Tracking
-- ========================================

-- Add stock management fields to products table
ALTER TABLE `products`
ADD COLUMN `track_stock` TINYINT(1) DEFAULT 1 COMMENT '1=Track stock, 0=Don''t track',
ADD COLUMN `low_stock_threshold` INT DEFAULT 5 COMMENT 'Alert when stock below this number',
ADD COLUMN `featured` TINYINT(1) DEFAULT 0 COMMENT '1=Featured product, 0=Regular';

-- Note: Stock quantity is already tracked in product_variants table
-- Each variant (size/color combination) has its own stock field

-- ========================================
-- HOW TO RUN THIS MIGRATION:
-- ========================================

-- FOR LOCALHOST (XAMPP):
-- ========================================
-- 1. Open phpMyAdmin: http://localhost/phpmyadmin
-- 2. Select database: athletesGym
-- 3. Go to "SQL" tab
-- 4. Copy and paste the ALTER TABLE command above
-- 5. Click "Go" button

-- FOR PRODUCTION (Hosting):
-- ========================================
-- 1. Login to cPanel → phpMyAdmin
-- 2. Select your production database
-- 3. Go to "SQL" tab
-- 4. Copy and paste the ALTER TABLE command above
-- 5. Click "Go" button

-- ========================================
-- VERIFICATION:
-- ========================================
-- Run this query to verify the new columns exist:
-- DESCRIBE products;
