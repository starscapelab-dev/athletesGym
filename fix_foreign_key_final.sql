-- COMPREHENSIVE FIX for guest checkout foreign key error
-- This script ensures the orders table properly supports NULL customer_id for guest users

-- Temporarily disable foreign key checks to avoid conflicts
SET FOREIGN_KEY_CHECKS=0;

-- Step 1: Drop the existing foreign key constraint
ALTER TABLE `orders` DROP FOREIGN KEY IF EXISTS `fk_orders_user`;

-- Step 2: Ensure customer_id column allows NULL (critical for guest checkout)
ALTER TABLE `orders` MODIFY COLUMN `customer_id` INT(11) NULL DEFAULT NULL;

-- Step 3: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Step 4: Recreate the foreign key with proper NULL handling
ALTER TABLE `orders`
ADD CONSTRAINT `fk_orders_user`
FOREIGN KEY (`customer_id`)
REFERENCES `users` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Verify the changes
SELECT
    COLUMN_NAME,
    IS_NULLABLE,
    COLUMN_TYPE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'orders'
AND COLUMN_NAME = 'customer_id';
