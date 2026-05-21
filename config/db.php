<?php
/**
 * Celesteà Zy - Database Connection Configuration (Modular Config Component)
 * Uses PDO for secure parameterized queries and exception handling.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'luna_etoile');

function getDBConnection($selectDb = true) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
        if ($selectDb) {
            $dsn .= ";dbname=" . DB_NAME;
        }
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        if ($selectDb) {
            // Self-healing database mechanism: dynamically detect and add user_id column to orders table if missing
            try {
                // Check if orders table exists
                $tableCheck = $pdo->query("SHOW TABLES LIKE 'orders'")->rowCount();
                if ($tableCheck > 0) {
                    // Check if user_id column exists
                    $columnCheck = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'user_id'")->rowCount();
                    if ($columnCheck === 0) {
                        // Ensure users table exists first so we can establish foreign key references
                        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                            `id` INT AUTO_INCREMENT PRIMARY KEY,
                            `name` VARCHAR(100) NOT NULL,
                            `email` VARCHAR(191) NOT NULL UNIQUE,
                            `password` VARCHAR(255) NOT NULL,
                            `role` VARCHAR(50) DEFAULT 'customer',
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                        
                        // Alter orders table to add user_id column
                        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `user_id` INT DEFAULT NULL AFTER `id`");
                        $pdo->exec("ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL");
                    }
                }
            } catch (Exception $ex) {
                // Silently bypass so it doesn't block loading if there's any temporary DDL lock
            }
        }
        
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failure to luxury database: " . $e->getMessage());
    }
}
?>
