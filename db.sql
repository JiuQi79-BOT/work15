-- 用户表
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nickname` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `verify_code` VARCHAR(64) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    `expires_at` DATETIME NOT NULL,
    INDEX `idx_nickname` (`nickname`),
    INDEX `idx_email` (`email`),
    INDEX `idx_verify_code` (`verify_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;