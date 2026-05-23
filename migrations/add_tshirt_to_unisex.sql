-- Add T-Shirt category to Unisex
-- First check if T-Shirt already exists in Unisex, if not, insert it
INSERT INTO `categories` (name, slug, image, gender, created_at)
SELECT 'T-Shirt', 't-shirt', 'uploads/categories/cat_5.png', 'Unisex', NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `categories` WHERE name = 'T-Shirt' AND gender = 'Unisex'
);
