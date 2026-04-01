-- Quick fix for guest checkout foreign key constraint error
-- Run this in phpMyAdmin or MySQL command line

-- Drop the existing foreign key constraint
ALTER TABLE `orders` DROP FOREIGN KEY `fk_orders_user`;

-- Make sure customer_id allows NULL values
ALTER TABLE `orders` MODIFY COLUMN `customer_id` INT(11) NULL;

-- Recreate the foreign key with proper NULL handling
ALTER TABLE `orders`
ADD CONSTRAINT `fk_orders_user`
FOREIGN KEY (`customer_id`)
REFERENCES `users` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;
