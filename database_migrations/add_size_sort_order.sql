-- Migration: Add sort_order column to sizes table for proper ordering
-- Date: 2026-05-16

-- Add sort_order column
ALTER TABLE `sizes` ADD COLUMN `sort_order` INT NOT NULL DEFAULT 999 AFTER `name`;

-- Update existing sizes with proper sort order
-- Standard sizes order: S(1), M(2), L(3), XL(4), XXL(5)
UPDATE `sizes` SET `sort_order` = 1 WHERE `name` = 'S';
UPDATE `sizes` SET `sort_order` = 2 WHERE `name` = 'M';
UPDATE `sizes` SET `sort_order` = 3 WHERE `name` = 'L';
UPDATE `sizes` SET `sort_order` = 4 WHERE `name` = 'XL';
UPDATE `sizes` SET `sort_order` = 5 WHERE `name` = 'XXL';

-- For custom sizes (S1, S2, M1, M2, Regular), keep high sort order so they appear last
UPDATE `sizes` SET `sort_order` = 10 WHERE `name` = 'S1';
UPDATE `sizes` SET `sort_order` = 11 WHERE `name` = 'S2';
UPDATE `sizes` SET `sort_order` = 12 WHERE `name` = 'M1';
UPDATE `sizes` SET `sort_order` = 13 WHERE `name` = 'M2';
UPDATE `sizes` SET `sort_order` = 20 WHERE `name` = 'Regular';

-- Add index for better performance
CREATE INDEX `idx_sort_order` ON `sizes`(`sort_order`);
