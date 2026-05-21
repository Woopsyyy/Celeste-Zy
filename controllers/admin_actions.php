<?php
/**
 * Celesteà Zy - Administrator AJAX Actions Controller
 * Handles administrative fulfillment mutations, product catalog insertions, and database seed resets.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Enforce admin permission guards
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Boutique Access Denied. Administrative clearance required.']);
    exit;
}

require_once '../config/db.php';

// Support both form-data and JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $_POST['action'] ?? $input['action'] ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing administrator action.']);
    exit;
}

try {
    $pdo = getDBConnection(true);

    // Verify that the admin user still exists in the database to prevent stale admin sessions
    $adminCheck = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `id` = ? AND `role` = 'admin'");
    $adminCheck->execute([$_SESSION['user_id']]);
    if ($adminCheck->fetchColumn() == 0) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Boutique Access Denied. Secure session has expired or admin profile is no longer registered.'
        ]);
        exit;
    }

    switch ($action) {
        case 'fulfill_order':
            $orderId = (int)($_POST['order_id'] ?? $input['order_id'] ?? 0);

            if ($orderId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Please provide a valid Order ID.']);
                exit;
            }

            // Verify order exists
            $checkStmt = $pdo->prepare("SELECT `status` FROM `orders` WHERE `id` = ?");
            $checkStmt->execute([$orderId]);
            $order = $checkStmt->fetch();

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order was not found in our registries.']);
                exit;
            }

            if ($order['status'] === 'fulfilled') {
                echo json_encode(['success' => false, 'message' => 'This order has already been fulfilled and shipped.']);
                exit;
            }

            // Update status to fulfilled
            $updateStmt = $pdo->prepare("UPDATE `orders` SET `status` = 'fulfilled' WHERE `id` = ?");
            $updateStmt->execute([$orderId]);

            echo json_encode([
                'success' => true,
                'message' => 'Order #' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . ' successfully transitioned to Fulfilled & Shipped state.'
            ]);
            break;

        case 'add_product':
            $name = trim($_POST['name'] ?? $input['name'] ?? '');
            $price = floatval($_POST['price'] ?? $input['price'] ?? 0);
            $subtitle = trim($_POST['subtitle'] ?? $input['subtitle'] ?? '');
            $description = trim($_POST['description'] ?? $input['description'] ?? '');
            $image_url = trim($_POST['image_url'] ?? $input['image_url'] ?? '');
            $top_notes = trim($_POST['top_notes'] ?? $input['top_notes'] ?? '');
            $heart_notes = trim($_POST['heart_notes'] ?? $input['heart_notes'] ?? '');
            $base_notes = trim($_POST['base_notes'] ?? $input['base_notes'] ?? '');
            $badge = trim($_POST['badge'] ?? $input['badge'] ?? null);
            
            $is_featured = isset($_POST['is_featured']) ? 1 : ($input['is_featured'] ?? 0);
            $is_bestseller = isset($_POST['is_bestseller']) ? 1 : ($input['is_bestseller'] ?? 0);

            if (empty($name) || $price <= 0 || empty($subtitle) || empty($description) || empty($image_url) || empty($top_notes) || empty($heart_notes) || empty($base_notes)) {
                echo json_encode(['success' => false, 'message' => 'All product details (excluding badge) must be specified.']);
                exit;
            }

            // Create URL slug
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\-]+/', '-', str_replace(' ', '-', $name)));
            // Strip duplicate dashes
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            // Verify unique slug
            $slugCheck = $pdo->prepare("SELECT COUNT(*) FROM `products` WHERE `slug` = ?");
            $slugCheck->execute([$slug]);
            if ($slugCheck->fetchColumn() > 0) {
                // Append random tag to ensure uniqueness
                $slug .= '-' . rand(100, 999);
            }

            // Insert product
            $sql = "INSERT INTO `products` 
                (`name`, `slug`, `price`, `image_url`, `subtitle`, `description`, `top_notes`, `heart_notes`, `base_notes`, `is_featured`, `is_bestseller`, `badge`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $name, $slug, $price, $image_url, $subtitle, $description,
                $top_notes, $heart_notes, $base_notes, $is_featured, $is_bestseller,
                empty($badge) ? null : $badge
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Exclusive fragrance "' . htmlspecialchars($name) . '" has been officially added to the boutique collections.'
            ]);
            break;

        case 'reset_seeds':
            // Trigger database setup re-seed logic
            // Since setup.php is in the root directory, let's reset table structures cleanly
            // Dropping is done under connection blocks
            $pdo->exec("DROP TABLE IF EXISTS `order_items`");
            $pdo->exec("DROP TABLE IF EXISTS `orders`");
            $pdo->exec("DROP TABLE IF EXISTS `products`");
            $pdo->exec("DROP TABLE IF EXISTS `newsletter`");
            $pdo->exec("DROP TABLE IF EXISTS `users`");

            // Re-create users
            $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(191) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role` VARCHAR(50) DEFAULT 'customer',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed user accounts
            $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
            $customerPass = password_hash('password123', PASSWORD_BCRYPT);
            $userStmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (?, ?, ?, ?)");
            $userStmt->execute(['Celesteà Zy Admin', 'admin', $adminPass, 'admin']);
            $userStmt->execute(['Celestial Customer', 'customer@celesteazy.com', $customerPass, 'customer']);

            // Re-create products
            $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL UNIQUE,
                `price` DECIMAL(10, 2) NOT NULL,
                `image_url` VARCHAR(255) NOT NULL,
                `subtitle` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL,
                `top_notes` VARCHAR(255) NOT NULL,
                `heart_notes` VARCHAR(255) NOT NULL,
                `base_notes` VARCHAR(255) NOT NULL,
                `is_featured` TINYINT(1) DEFAULT 0,
                `is_bestseller` TINYINT(1) DEFAULT 0,
                `badge` VARCHAR(50) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed products
            $seeds = [
                ['Noir Éclipse', 'noir-eclipse', 4800.00, 'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=1200&auto=format&fit=crop', 'Woody & Mysterious Oriental', 'An intense encounter between shadowed velvet fruits, raw warm amber, and aged premium smoky woods.', 'Spiced Black Cherry, Saffron', 'Midnight Orchid, Velvet Rose', 'Warm Ambergris, Smoky Cedarwood', 1, 0, null],
                ['Or Blanc', 'or-blanc', 5200.00, 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=1200&auto=format&fit=crop', 'Fresh Floral Aldehyde', 'A bright, radiant glow of white blossoms shimmering against crystal vanilla musk under stardust.', 'Aldehydic Accord, Calabrian Bergamot', 'White Jasmine Bloom, Lily of the Valley', 'Vanilla Pod, Soft White Musk', 1, 0, null],
                ['Soleil de Nuit', 'soleil-de-nuit', 4950.00, 'https://images.unsplash.com/photo-1617897903246-719242758050?q=80&w=1200&auto=format&fit=crop', 'Rich Warm Oud Saffron', 'A heavy sensual dream of gold-dusted spices, crimson rose petals, and midnight tobacco.', 'Pink Pepper, Cardamom Seed', 'Black Fig, Crimson Rose Petals', 'Sandalwood, Tobacco Accord', 1, 0, null],
                ['Celesteà Intense', 'celestea-intense', 5400.00, 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?q=80&w=1200&auto=format&fit=crop', 'Midnight Rose & Intense Patchouli', 'An absolute masterwork capture of night-blooming dark roses, dry vetiver, and premium heavy patchouli resin.', 'Wild Raspberry, Cardamom', 'Grandiflorum Jasmine, Midnight Rose', 'Indonesian Patchouli, Tonka Bean', 0, 1, 'Signature'],
                ['Mystère de Nuit', 'mystere-de-nuit', 4990.00, 'https://images.unsplash.com/photo-1616949755610-8c9bbc08f138?q=80&w=1200&auto=format&fit=crop', 'Smoked Cardamom & Velvet Cashmere', 'A quiet whispers trace of dark vanilla pods, aged incense smoke, and cashmere woods wrapping the skin.', 'Olibanum incense, Pink Pepper', 'Soft Cashmere Woods, Cypriol oil', 'Madagascar Vanilla Pod, Vetiver', 0, 1, 'Trending'],
                ['Poudre de Satin', 'poudre-de-satin', 4600.00, 'https://images.unsplash.com/photo-1585218356057-dc0e8d3558bb?q=80&w=1200&auto=format&fit=crop', 'Creamy Powdered Vanilla & Musk', 'Satin sheets captured in olfactory beauty. Soft iris petals blended with warm roasted almonds and clean cedar.', 'Warm Almond, White Iris', 'Helicotrope, Mimosa Blossoms', 'Sandalwood, Clean Musk Accord', 0, 1, 'Vintage'],
                ['Rose Impériale', 'rose-imperiale', 5100.00, 'https://images.unsplash.com/photo-1547887537-6158d64c35b3?q=80&w=1200&auto=format&fit=crop', 'Deep Damascus Rose & Golden Amber', 'Royal amber streams flowing around majestic hand-pressed Damascus rose oil and dry agarwood dust.', 'Coriander, Honey Glaze', 'Damascus Rose Absolute, Cinnamon', 'Golden Baltic Amber, Somalian Myrrh', 0, 1, 'Rare Edition']
            ];

            $insertStmt = $pdo->prepare("INSERT INTO `products` 
                (`name`, `slug`, `price`, `image_url`, `subtitle`, `description`, `top_notes`, `heart_notes`, `base_notes`, `is_featured`, `is_bestseller`, `badge`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($seeds as $product) {
                $insertStmt->execute($product);
            }

            // Re-create orders & items
            $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT DEFAULT NULL,
                `total_price` DECIMAL(10, 2) NOT NULL,
                `shipping_price` DECIMAL(10, 2) NOT NULL,
                `status` VARCHAR(50) DEFAULT 'pending',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `order_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `quantity` INT NOT NULL,
                `price` DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Re-create newsletter
            $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(191) NOT NULL UNIQUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            echo json_encode([
                'success' => true,
                'message' => 'Haute database successfully reset and fully re-seeded to factory metrics.'
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Administrative request action is unrecognized.']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Administrative transaction failed: ' . $e->getMessage()
    ]);
}
?>
