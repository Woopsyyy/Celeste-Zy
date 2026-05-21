<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Celesteà Zy | Database Initializer</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300&family=Montserrat:wght@200;300;400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #080809;
      color: #f9f8f6;
      font-family: 'Montserrat', sans-serif;
      font-weight: 300;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      overflow: hidden;
    }
    .setup-container {
      max-width: 650px;
      width: 90%;
      background: #121214;
      border: 1px solid rgba(197, 168, 128, 0.2);
      padding: 60px 40px;
      text-align: center;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8);
      position: relative;
    }
    .setup-container::before {
      content: '';
      position: absolute;
      inset: 6px;
      border: 1px solid rgba(197, 168, 128, 0.08);
      pointer-events: none;
    }
    .icon {
      font-size: 3rem;
      color: #c5a880;
      margin-bottom: 25px;
    }
    h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.8rem;
      font-weight: 400;
      letter-spacing: 2px;
      margin-bottom: 15px;
    }
    h1 span {
      color: #c5a880;
    }
    p {
      color: #8e8e93;
      font-size: 0.9rem;
      line-height: 1.8;
      margin-bottom: 35px;
    }
    .logs {
      background: #080809;
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 4px;
      padding: 20px;
      text-align: left;
      font-family: monospace;
      font-size: 0.8rem;
      max-height: 200px;
      overflow-y: auto;
      margin-bottom: 40px;
      color: #ebdcb9;
    }
    .log-entry {
      margin-bottom: 8px;
    }
    .log-success {
      color: #c5a880;
    }
    .log-error {
      color: #df4747;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 16px 36px;
      background: #c5a880;
      color: #080809;
      font-weight: 400;
      letter-spacing: 2px;
      text-transform: uppercase;
      text-decoration: none;
      font-size: 0.85rem;
      transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1px solid #c5a880;
    }
    .btn:hover {
      background: transparent;
      color: #c5a880;
      box-shadow: 0 10px 25px rgba(197, 168, 128, 0.2);
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <div class="setup-container">
    <div class="icon">
      <i class="fa-solid fa-database"></i>
    </div>
    <h1>Celesteà<span>Zy</span></h1>
    <p>Haute Parfumerie Database Installation System</p>

    <div class="logs">
      <?php
      require_once 'config/db.php';
      
      $logs = [];
      $hasError = false;

      function logMsg($msg, $type = 'info') {
          global $logs;
          $class = 'log-info';
          if ($type === 'success') $class = 'log-success';
          if ($type === 'error') $class = 'log-error';
          $logs[] = "<div class='log-entry {$class}'>" . htmlspecialchars($msg) . "</div>";
      }

      try {
          // 1. Connect without Database context to create the DB if needed
          logMsg("Establishing initial database context connection...");
          $pdo = getDBConnection(false);
          logMsg("Successfully connected to MySQL database engine.", "success");

          // 2. Create database
          logMsg("Ensuring database 'luna_etoile' exists...");
          $pdo->exec("CREATE DATABASE IF NOT EXISTS `luna_etoile` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
          logMsg("Database 'luna_etoile' is verified/created.", "success");

          // 3. Connect to database context
          $pdo = getDBConnection(true);

          // Drop tables in order of dependency to ensure clean rebuild
          logMsg("Resetting table structures for a clean database install...");
          $pdo->exec("DROP TABLE IF EXISTS `order_items`");
          $pdo->exec("DROP TABLE IF EXISTS `orders`");
          $pdo->exec("DROP TABLE IF EXISTS `products`");
          $pdo->exec("DROP TABLE IF EXISTS `newsletter`");
          $pdo->exec("DROP TABLE IF EXISTS `users`");

          // 4. Create users table
          logMsg("Creating table 'users'...");
          $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(100) NOT NULL,
              `email` VARCHAR(191) NOT NULL UNIQUE,
              `password` VARCHAR(255) NOT NULL,
              `role` VARCHAR(50) DEFAULT 'customer',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
          logMsg("Table 'users' created/verified.", "success");

          // 5. Create products table
          logMsg("Creating table 'products'...");
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
          logMsg("Table 'products' created/verified.", "success");

          // 6. Create newsletter table
          logMsg("Creating table 'newsletter'...");
          $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `email` VARCHAR(191) NOT NULL UNIQUE,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
          logMsg("Table 'newsletter' created/verified.", "success");

          // 7. Create orders table
          logMsg("Creating table 'orders'...");
          $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT DEFAULT NULL,
              `total_price` DECIMAL(10, 2) NOT NULL,
              `shipping_price` DECIMAL(10, 2) NOT NULL,
              `status` VARCHAR(50) DEFAULT 'pending',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
          logMsg("Table 'orders' created/verified.", "success");

          // 7. Create order items table
          logMsg("Creating table 'order_items'...");
          $pdo->exec("CREATE TABLE IF NOT EXISTS `order_items` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `order_id` INT NOT NULL,
              `product_id` INT NOT NULL,
              `quantity` INT NOT NULL,
              `price` DECIMAL(10, 2) NOT NULL,
              FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
              FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
          logMsg("Table 'order_items' created/verified.", "success");

          // 8. Seeding dynamic product catalog
          logMsg("Checking for initial product seed records...");
          $stmt = $pdo->query("SELECT COUNT(*) FROM `products`");
          $count = $stmt->fetchColumn();

          if ($count == 0) {
              logMsg("No products found in DB. Commencing luxury product seed injection...");
              
              $seeds = [
                  // Signature Trilogy
                  [
                      'Noir Éclipse', 'noir-eclipse', 4800.00, 
                      'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=1200&auto=format&fit=crop',
                      'Woody & Mysterious Oriental',
                      'An intense encounter between shadowed velvet fruits, raw warm amber, and aged premium smoky woods.',
                      'Spiced Black Cherry, Saffron', 'Midnight Orchid, Velvet Rose', 'Warm Ambergris, Smoky Cedarwood',
                      1, 0, null
                  ],
                  [
                      'Or Blanc', 'or-blanc', 5200.00,
                      'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=1200&auto=format&fit=crop',
                      'Fresh Floral Aldehyde',
                      'A bright, radiant glow of white blossoms shimmering against crystal vanilla musk under stardust.',
                      'Aldehydic Accord, Calabrian Bergamot', 'White Jasmine Bloom, Lily of the Valley', 'Vanilla Pod, Soft White Musk',
                      1, 0, null
                  ],
                  [
                      'Soleil de Nuit', 'soleil-de-nuit', 4950.00,
                      'https://images.unsplash.com/photo-1617897903246-719242758050?q=80&w=1200&auto=format&fit=crop',
                      'Rich Warm Oud Saffron',
                      'A heavy sensual dream of gold-dusted spices, crimson rose petals, and midnight tobacco.',
                      'Pink Pepper, Cardamom Seed', 'Black Fig, Crimson Rose Petals', 'Sandalwood, Tobacco Accord',
                      1, 0, null
                  ],
                  // Bestsellers
                  [
                      'Celesteà Intense', 'celestea-intense', 5400.00,
                      'https://images.unsplash.com/photo-1523293182086-7651a899d37f?q=80&w=1200&auto=format&fit=crop',
                      'Midnight Rose & Intense Patchouli',
                      'An absolute masterwork capture of night-blooming dark roses, dry vetiver, and premium heavy patchouli resin.',
                      'Wild Raspberry, Cardamom', 'Grandiflorum Jasmine, Midnight Rose', 'Indonesian Patchouli, Tonka Bean',
                      0, 1, 'Signature'
                  ],
                  [
                      'Mystère de Nuit', 'mystere-de-nuit', 4990.00,
                      'https://images.unsplash.com/photo-1616949755610-8c9bbc08f138?q=80&w=1200&auto=format&fit=crop',
                      'Smoked Cardamom & Velvet Cashmere',
                      'A quiet whispers trace of dark vanilla pods, aged incense smoke, and cashmere woods wrapping the skin.',
                      'Olibanum incense, Pink Pepper', 'Soft Cashmere Woods, Cypriol oil', 'Madagascar Vanilla Pod, Vetiver',
                      0, 1, 'Trending'
                  ],
                  [
                      'Poudre de Satin', 'poudre-de-satin', 4600.00,
                      'https://images.unsplash.com/photo-1585218356057-dc0e8d3558bb?q=80&w=1200&auto=format&fit=crop',
                      'Creamy Powdered Vanilla & Musk',
                      'Satin sheets captured in olfactory beauty. Soft iris petals blended with warm roasted almonds and clean cedar.',
                      'Warm Almond, White Iris', 'Helicotrope, Mimosa Blossoms', 'Sandalwood, Clean Musk Accord',
                      0, 1, 'Vintage'
                  ],
                  [
                      'Rose Impériale', 'rose-imperiale', 5100.00,
                      'https://images.unsplash.com/photo-1547887537-6158d64c35b3?q=80&w=1200&auto=format&fit=crop',
                      'Deep Damascus Rose & Golden Amber',
                      'Royal amber streams flowing around majestic hand-pressed Damascus rose oil and dry agarwood dust.',
                      'Coriander, Honey Glaze', 'Damascus Rose Absolute, Cinnamon', 'Golden Baltic Amber, Somalian Myrrh',
                      0, 1, 'Rare Edition'
                  ]
              ];

              $insertSql = "INSERT INTO `products` 
                  (`name`, `slug`, `price`, `image_url`, `subtitle`, `description`, `top_notes`, `heart_notes`, `base_notes`, `is_featured`, `is_bestseller`, `badge`)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
              $insertStmt = $pdo->prepare($insertSql);

              foreach ($seeds as $product) {
                  $insertStmt->execute($product);
                  logMsg("Successfully injected: " . $product[0], "success");
              }
              logMsg("Luxury product seed data populated successfully.", "success");
          } else {
              logMsg("Database contains existing products ($count items). Seed injection bypassed.");
          }

          // 8b. Seeding premium user profiles
          logMsg("Checking for initial user records...");
          $userCount = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
          if ($userCount == 0) {
              logMsg("Injecting premium admin and customer profiles...");
              $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
              $customerPass = password_hash('password123', PASSWORD_BCRYPT);
              
              $userStmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (?, ?, ?, ?)");
              $userStmt->execute(['Celesteà Zy Admin', 'admin', $adminPass, 'admin']);
              $userStmt->execute(['Celestial Customer', 'customer@celesteazy.com', $customerPass, 'customer']);
              logMsg("Luxury Admin and Customer profiles successfully seeded.", "success");
          } else {
              logMsg("Database contains existing user profiles. Seeding bypassed.");
          }

          logMsg("Installation Completed Successfully!", "success");

      } catch (Exception $e) {
          $hasError = true;
          logMsg("FATAL SETUP EXCEPTION: " . $e->getMessage(), "error");
      }

      // Output logs
      foreach ($logs as $log) {
          echo $log;
      }
      ?>
    </div>

    <?php if (!$hasError): ?>
      <a href="index.php" class="btn">Enter Boutique</a>
    <?php else: ?>
      <p style="color: #df4747;"><i class="fa-solid fa-circle-exclamation"></i> Installation encountered a critical failure. Verify XAMPP MySQL is active.</p>
    <?php endif; ?>
  </div>

</body>
</html>
