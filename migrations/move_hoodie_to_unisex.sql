-- Move Hoodie category from Men to Unisex
UPDATE `categories` SET gender = 'Unisex' WHERE id = 3 AND name = 'Hoodie';
