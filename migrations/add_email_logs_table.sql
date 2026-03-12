-- Migration: Add email_logs table for tracking email sending
-- Purpose: Track admin notifications and other important emails for monitoring and debugging

CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED,
  `recipient` VARCHAR(255) NOT NULL,
  `subject` TEXT,
  `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `error_message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
);
