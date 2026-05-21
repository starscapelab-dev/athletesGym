-- Add Unisex category and sample unisex products

INSERT INTO `categories` (name, image, slug, gender)
SELECT * FROM (SELECT 'Hoodies' AS name, NULL AS image, 'hoodies' AS slug, 'Unisex' AS gender) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE name = 'Hoodies' AND gender = 'Unisex'
) LIMIT 1;

-- Insert sample unisex products if not present
INSERT INTO `products` (name, description, category_id, category, gender, price, active, created_at)
SELECT * FROM (
  SELECT 'Classic Unisex Hoodie' AS name, 'Comfortable unisex hoodie' AS description,
         (SELECT id FROM categories WHERE name='Hoodies' AND gender='Unisex' LIMIT 1) AS category_id,
         'Hoodies' AS category, 'unisex' AS gender, 320.00 AS price, '1' AS active, NOW() AS created_at
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM products WHERE name='Classic Unisex Hoodie' AND gender='unisex'
) LIMIT 1;

INSERT INTO `products` (name, description, category_id, category, gender, price, active, created_at)
SELECT * FROM (
  SELECT 'Unisex Oversized Tee' AS name, 'Relaxed fit oversized t-shirt for everyone' AS description,
         (SELECT id FROM categories WHERE name='Hoodies' AND gender='Unisex' LIMIT 1) AS category_id,
         'Hoodies' AS category, 'unisex' AS gender, 180.00 AS price, '1' AS active, NOW() AS created_at
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM products WHERE name='Unisex Oversized Tee' AND gender='unisex'
) LIMIT 1;

-- Note: product images can be added via admin panel; placeholder image will be used until added.
