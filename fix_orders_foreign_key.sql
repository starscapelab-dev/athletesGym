-- Fix foreign key constraint for orders.customer_id to allow NULL values for guest users
-- This allows guest checkout to work properly

-- Step 1: Drop the existing foreign key constraint if it exists
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'orders'
    AND CONSTRAINT_NAME = 'fk_orders_user'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

-- Drop constraint if exists
SET @sql_drop = IF(@constraint_exists > 0,
    'ALTER TABLE `orders` DROP FOREIGN KEY `fk_orders_user`',
    'SELECT "Constraint fk_orders_user does not exist"'
);
PREPARE stmt FROM @sql_drop;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Make sure customer_id column allows NULL
ALTER TABLE `orders`
MODIFY COLUMN `customer_id` INT(11) NULL;

-- Step 3: Recreate the foreign key constraint with proper NULL handling
ALTER TABLE `orders`
ADD CONSTRAINT `fk_orders_user`
FOREIGN KEY (`customer_id`)
REFERENCES `users` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Verify the change
SELECT
    COLUMN_NAME,
    IS_NULLABLE,
    COLUMN_TYPE,
    COLUMN_KEY
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'orders'
AND COLUMN_NAME = 'customer_id';

-- Show foreign key constraints
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'orders'
AND CONSTRAINT_NAME = 'fk_orders_user';
