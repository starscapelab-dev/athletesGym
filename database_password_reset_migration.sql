-- ========================================
-- Database Migration for Password Reset Feature
-- Athletes Gym - Password Reset OTP Columns
-- ========================================

-- Add OTP columns to users table if they don't exist
-- These columns are required for the forgot password functionality

-- Check if columns exist first (for safety)
-- Run this script in phpMyAdmin or MySQL command line

USE wkfbjlmy_WPATH;

-- Add reset_otp column (stores 6-digit OTP code)
ALTER TABLE users
ADD COLUMN IF NOT EXISTS reset_otp VARCHAR(6) DEFAULT NULL
COMMENT 'Stores 6-digit OTP for password reset';

-- Add reset_expires column (stores OTP expiry time)
ALTER TABLE users
ADD COLUMN IF NOT EXISTS reset_expires DATETIME DEFAULT NULL
COMMENT 'Stores expiry time for reset OTP (10 minutes from generation)';

-- Verify columns were added
DESCRIBE users;

-- Sample output should include:
-- reset_otp       | varchar(6)  | YES  |     | NULL    |
-- reset_expires   | datetime    | YES  |     | NULL    |

-- ========================================
-- INSTRUCTIONS:
-- ========================================
--
-- 1. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Select database: wkfbjlmy_WPATH
-- 3. Go to "SQL" tab
-- 4. Copy and paste this entire script
-- 5. Click "Go" to execute
--
-- OR via command line:
-- mysql -u root wkfbjlmy_WPATH < database_password_reset_migration.sql
--
-- ========================================
-- FOR PRODUCTION (Hosting):
-- ========================================
--
-- 1. Login to cPanel → phpMyAdmin
-- 2. Select your production database
-- 3. Replace 'wkfbjlmy_WPATH' with your production database name
-- 4. Run the ALTER TABLE commands above
--
-- ========================================
