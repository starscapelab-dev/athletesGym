-- Add featured/thumbnail image functionality to product_images table

-- Add is_featured column to product_images table
ALTER TABLE `product_images`
ADD COLUMN `is_featured` TINYINT(1) DEFAULT 0 AFTER `alt_text`;

-- Add index for faster featured image queries
ALTER TABLE `product_images`
ADD INDEX `idx_is_featured` (`product_id`, `is_featured`);

-- Set the first image of each product as featured by default
UPDATE `product_images` pi1
SET `is_featured` = 1
WHERE `id` = (
    SELECT MIN(`id`)
    FROM `product_images` pi2
    WHERE pi2.`product_id` = pi1.`product_id`
);
