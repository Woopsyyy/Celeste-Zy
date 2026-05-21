<?php
/**
 * Celesteà Zy - Haute Parfumerie Admin Control Console
 * Protected control deck managing luxury order fulfillment, catalog seeding, and subscriber registries.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Enforce active session with Admin clearances
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

require_once 'config/db.php';
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
    header("Location: index.php");
    exit;
}

// 1. Core Metrics Audits
$totalRevenue = $pdo->query("SELECT SUM(`total_price`) FROM `orders` WHERE `status` = 'fulfilled'")->fetchColumn() ?: 0.00;
$totalOrders = $pdo->query("SELECT COUNT(*) FROM `orders`")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM `products`")->fetchColumn();
$totalSubscribers = $pdo->query("SELECT COUNT(*) FROM `newsletter`")->fetchColumn();

// 2. Fetch Customer Order Logs
$ordersStmt = $pdo->query("
    SELECT o.*, u.name AS customer_name, u.email AS customer_email
    FROM `orders` o
    LEFT JOIN `users` u ON o.user_id = u.id
    ORDER BY o.id DESC
");
$orders = $ordersStmt->fetchAll();

// 3. Gather Order Items mapping
$itemsStmt = $pdo->query("
    SELECT oi.*, p.name AS product_name 
    FROM `order_items` oi
    JOIN `products` p ON oi.product_id = p.id
");
$allItems = $itemsStmt->fetchAll();

$orderItemsMap = [];
foreach ($allItems as $item) {
    $orderItemsMap[$item['order_id']][] = $item;
}

// 4. Gather Newsletter Club subscribers
$subscribers = $pdo->query("SELECT * FROM `newsletter` ORDER BY `id` DESC")->fetchAll();

// 5. Gather registered users
$registeredUsers = $pdo->query("SELECT `id`, `name`, `email`, `role`, `created_at` FROM `users` ORDER BY `id` DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Celesteà Zy | Administrative Console</title>
  
  <!-- Premium Typography and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --matte-black: #080809;
      --charcoal: #121214;
      --deep-charcoal: #18181b;
      --warm-gold: #c5a880;
      --gold-bright: #d4af37;
      --champagne-beige: #ebdcb9;
      --soft-ivory: #f9f8f6;
      --muted-gray: #8e8e93;
      --border-color: rgba(197, 168, 128, 0.15);
      --font-serif: 'Cormorant Garamond', serif;
      --font-sans: 'Montserrat', sans-serif;
      --transition-smooth: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Custom scrollbars */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: var(--matte-black);
    }
    ::-webkit-scrollbar-thumb {
      background: var(--warm-gold);
      border-radius: 3px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: var(--matte-black);
      color: var(--soft-ivory);
      font-family: var(--font-sans);
      font-weight: 300;
      min-height: 100vh;
      line-height: 1.6;
    }

    header {
      background: var(--charcoal);
      border-bottom: 1px solid var(--border-color);
      padding: 20px 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .admin-logo {
      display: flex;
      flex-direction: column;
    }

    .admin-logo a {
      font-size: 1.6rem;
      font-family: var(--font-serif);
      letter-spacing: 3px;
      color: var(--soft-ivory);
      text-decoration: none;
      text-transform: uppercase;
    }

    .admin-logo span {
      color: var(--warm-gold);
    }

    .admin-logo-sub {
      font-size: 0.6rem;
      text-transform: uppercase;
      letter-spacing: 3px;
      color: var(--warm-gold);
      margin-top: -2px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .btn-return {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--muted-gray);
      text-decoration: none;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      transition: var(--transition-smooth);
    }

    .btn-return:hover {
      color: var(--warm-gold);
      transform: translateX(-3px);
    }

    .admin-badge {
      font-size: 0.65rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 5px 12px;
      background: rgba(197, 168, 128, 0.12);
      border: 1px solid var(--border-color);
      color: var(--warm-gold);
      border-radius: 2px;
    }

    .dashboard-container {
      max-width: 1400px;
      margin: 40px auto;
      padding: 0 4%;
    }

    /* KPI Deck */
    .kpi-deck {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 30px;
      margin-bottom: 45px;
    }

    .kpi-card {
      background: var(--charcoal);
      border: 1px solid rgba(197, 168, 128, 0.1);
      padding: 30px;
      border-radius: 2px;
      position: relative;
      overflow: hidden;
      transition: var(--transition-smooth);
    }

    .kpi-card:hover {
      border-color: var(--warm-gold);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      transform: translateY(-3px);
    }

    .kpi-card::before {
      content: '';
      position: absolute;
      inset: 4px;
      border: 1px solid rgba(197, 168, 128, 0.03);
      pointer-events: none;
    }

    .kpi-icon {
      position: absolute;
      right: 25px;
      bottom: 20px;
      font-size: 2.8rem;
      color: var(--warm-gold);
      opacity: 0.08;
    }

    .kpi-label {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--muted-gray);
      margin-bottom: 10px;
    }

    .kpi-val {
      font-family: var(--font-serif);
      font-size: 2.2rem;
      color: var(--soft-ivory);
      font-weight: 300;
    }

    .kpi-val span {
      color: var(--warm-gold);
    }

    /* Panel Tabs */
    .deck-tabs {
      display: flex;
      gap: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      margin-bottom: 35px;
      overflow-x: auto;
      padding-bottom: 1px;
    }

    .tab-btn {
      background: none;
      border: none;
      color: var(--muted-gray);
      font-family: var(--font-sans);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      padding: 15px 25px;
      cursor: pointer;
      transition: var(--transition-smooth);
      position: relative;
      white-space: nowrap;
    }

    .tab-btn.active {
      color: var(--warm-gold);
    }

    .tab-btn.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background: var(--warm-gold);
    }

    .panel-content {
      display: none;
    }

    .panel-content.active {
      display: block;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Luxury Tables */
    .luxury-table-wrapper {
      width: 100%;
      overflow-x: auto;
      background: var(--charcoal);
      border: 1px solid rgba(255,255,255,0.05);
      margin-bottom: 40px;
    }

    .luxury-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.85rem;
    }

    .luxury-table th {
      background: rgba(0, 0, 0, 0.3);
      border-bottom: 1px solid var(--border-color);
      color: var(--warm-gold);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: 0.75rem;
      padding: 18px 24px;
    }

    .luxury-table td {
      padding: 18px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.03);
      vertical-align: middle;
      color: #dfddd8;
    }

    .luxury-table tr:hover td {
      background: rgba(197, 168, 128, 0.015);
      color: var(--soft-ivory);
    }

    /* Pill Badges */
    .status-pill {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .status-pill.pending {
      background: rgba(245, 166, 35, 0.1);
      color: #f5a623;
      border: 1px solid rgba(245, 166, 35, 0.2);
    }

    .status-pill.fulfilled {
      background: rgba(197, 168, 128, 0.12);
      color: var(--warm-gold);
      border: 1px solid var(--border-color);
    }

    /* Admin Action buttons */
    .btn-action {
      background: transparent;
      border: 1px solid var(--warm-gold);
      color: var(--warm-gold);
      padding: 8px 16px;
      font-size: 0.75rem;
      font-family: var(--font-sans);
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      transition: var(--transition-smooth);
    }

    .btn-action:hover {
      background: var(--warm-gold);
      color: var(--matte-black);
      box-shadow: 0 4px 15px rgba(197, 168, 128, 0.25);
    }

    .btn-action:disabled {
      border-color: rgba(255, 255, 255, 0.1);
      color: var(--muted-gray);
      cursor: not-allowed;
    }

    /* Form Panels */
    .form-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 40px;
    }

    @media (max-width: 950px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
    }

    .luxury-card {
      background: var(--charcoal);
      border: 1px solid rgba(197, 168, 128, 0.15);
      padding: 40px;
      position: relative;
      margin-bottom: 40px;
    }

    .luxury-card::before {
      content: '';
      position: absolute;
      inset: 6px;
      border: 1px solid rgba(197, 168, 128, 0.05);
      pointer-events: none;
    }

    .luxury-card h3 {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      margin-bottom: 25px;
      letter-spacing: 1.5px;
      color: var(--soft-ivory);
      border-bottom: 1px solid rgba(255,255,255,0.05);
      padding-bottom: 15px;
    }

    .form-group-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--muted-gray);
      margin-bottom: 8px;
    }

    .form-control {
      width: 100%;
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(197, 168, 128, 0.15);
      color: var(--soft-ivory);
      padding: 12px 16px;
      font-family: var(--font-sans);
      font-size: 0.8rem;
      font-weight: 300;
      transition: var(--transition-smooth);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--warm-gold);
      background: rgba(0,0,0,0.5);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 15px;
    }

    .checkbox-group input {
      accent-color: var(--warm-gold);
      cursor: pointer;
      width: 16px;
      height: 16px;
    }

    .checkbox-group label {
      margin-bottom: 0;
      cursor: pointer;
    }

    .btn-submit {
      display: inline-flex;
      padding: 14px 35px;
      background: var(--warm-gold);
      color: var(--matte-black);
      border: 1px solid var(--warm-gold);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition-smooth);
    }

    .btn-submit:hover {
      background: transparent;
      color: var(--warm-gold);
      box-shadow: 0 8px 25px rgba(197, 168, 128, 0.15);
    }

    .feedback-note {
      padding: 15px;
      font-size: 0.8rem;
      margin-top: 20px;
      display: none;
    }

    .feedback-note.success {
      display: block;
      background: rgba(197, 168, 128, 0.08);
      border: 1px solid var(--warm-gold);
      color: var(--warm-gold);
    }

    .feedback-note.error {
      display: block;
      background: rgba(223, 71, 71, 0.08);
      border: 1px solid #df4747;
      color: #df4747;
    }

    /* Database Reset Card */
    .reset-warning-card {
      background: rgba(223, 71, 71, 0.03);
      border: 1px solid rgba(223, 71, 71, 0.15);
      padding: 30px 40px;
      margin-top: 40px;
      position: relative;
    }

    .reset-warning-card h4 {
      color: #df4747;
      font-size: 1.1rem;
      margin-bottom: 12px;
      letter-spacing: 1px;
    }

    .reset-warning-card p {
      color: var(--muted-gray);
      font-size: 0.8rem;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .btn-reset {
      border: 1px solid #df4747;
      color: #df4747;
      background: transparent;
      padding: 10px 22px;
      font-size: 0.75rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      transition: var(--transition-smooth);
    }

    .btn-reset:hover {
      background: #df4747;
      color: var(--soft-ivory);
      box-shadow: 0 5px 15px rgba(223, 71, 71, 0.2);
    }
  </style>
</head>
<body>

  <!-- ==========================================
       ADMIN CONSOLE HEADER
       ========================================== -->
  <header>
    <div class="admin-logo">
      <a href="index.php">Celesteà<span>Zy</span></a>
      <span class="admin-logo-sub">Administrative Deck</span>
    </div>

    <div class="header-actions">
      <span class="admin-badge"><i class="fa-solid fa-crown"></i> Concierge Admin</span>
      <a href="index.php" class="btn-return"><i class="fa-solid fa-arrow-left"></i> View Store</a>
    </div>
  </header>

  <!-- ==========================================
       DASHBOARD WORKSPACE
       ========================================== -->
  <div class="dashboard-container">
    
    <!-- KPI metrics summary deck -->
    <div class="kpi-deck">
      <div class="kpi-card">
        <i class="fa-solid fa-coins kpi-icon"></i>
        <div class="kpi-label">Luxury Fulfilled Revenue</div>
        <div class="kpi-val">₱<span><?php echo number_format($totalRevenue, 2); ?></span></div>
      </div>

      <div class="kpi-card">
        <i class="fa-solid fa-truck-ramp-box kpi-icon"></i>
        <div class="kpi-label">Concierge Orders Volume</div>
        <div class="kpi-val"><?php echo $totalOrders; ?> <span>orders</span></div>
      </div>

      <div class="kpi-card">
        <i class="fa-solid fa-bottle-droplet kpi-icon"></i>
        <div class="kpi-label">Fragrance Catalog</div>
        <div class="kpi-val"><?php echo $totalProducts; ?> <span>items</span></div>
      </div>

      <div class="kpi-card">
        <i class="fa-solid fa-user-group kpi-icon"></i>
        <div class="kpi-label">The Club Audience</div>
        <div class="kpi-val"><?php echo $totalSubscribers + count($registeredUsers); ?> <span>members</span></div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="deck-tabs">
      <button class="tab-btn active" data-panel="panelOrders">Fulfillment Center</button>
      <button class="tab-btn" data-panel="panelCatalog">Fragrance Designer</button>
      <button class="tab-btn" data-panel="panelAudience">Member Registries</button>
    </div>

    <!-- PANEL: Fulfillment Center (Active Orders) -->
    <div class="panel-content active" id="panelOrders">
      <div class="orders-section-title" style="margin-bottom: 25px; font-size: 1.8rem; font-family: var(--font-serif);">
        Boutique Fulfillment Registry
      </div>

      <div class="luxury-table-wrapper">
        <table class="luxury-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer Profile</th>
              <th>Ordered Selections</th>
              <th>Date & Stamp</th>
              <th>Fulfillment Status</th>
              <th>Grand Total</th>
              <th>Concierge Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($orders)): ?>
              <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: var(--muted-gray); padding: 40px 0;">
                  No boutique orders have been recorded in the registry yet.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($orders as $order): ?>
                <tr id="orderRow-<?php echo $order['id']; ?>">
                  <td style="font-family: monospace; font-size: 0.9rem; color: var(--warm-gold);">
                    #CZ-260<?php echo $order['id']; ?>
                  </td>
                  <td>
                    <strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Walk-in Guest'); ?></strong><br>
                    <span style="font-size: 0.75rem; color: var(--muted-gray);"><?php echo htmlspecialchars($order['customer_email'] ?? 'unregistered@customer.com'); ?></span>
                  </td>
                  <td>
                    <div style="font-size: 0.8rem; line-height: 1.5;">
                      <?php 
                      $items = $orderItemsMap[$order['id']] ?? [];
                      foreach ($items as $item) {
                          echo htmlspecialchars($item['product_name']) . " <span style='color: var(--warm-gold);'>x" . $item['quantity'] . "</span><br>";
                      }
                      ?>
                    </div>
                  </td>
                  <td style="font-size: 0.8rem; color: var(--muted-gray);">
                    <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                  </td>
                  <td>
                    <span class="status-pill <?php echo htmlspecialchars($order['status']); ?>" id="statusPill-<?php echo $order['id']; ?>">
                      <i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                  </td>
                  <td style="font-family: var(--font-serif); font-size: 1.1rem; color: var(--warm-gold);">
                    ₱<?php echo number_format($order['total_price'], 2); ?>
                  </td>
                  <td>
                    <?php if ($order['status'] === 'pending'): ?>
                      <button class="btn-action btn-fulfill" data-id="<?php echo $order['id']; ?>">
                        Ship Order
                      </button>
                    <?php else: ?>
                      <button class="btn-action" disabled>
                        Shipped
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PANEL: Fragrance Designer (Add New Products) -->
    <div class="panel-content" id="panelCatalog">
      <div class="form-grid">
        
        <!-- Left form -->
        <div class="luxury-card">
          <h3>Register High-Fashion Fragrance</h3>
          
          <form id="formAddProduct" novalidate>
            <input type="hidden" name="action" value="add_product">

            <div class="form-group-row">
              <div class="form-group">
                <label for="p_name">Olfactory Name</label>
                <input type="text" id="p_name" name="name" class="form-control" placeholder="E.g. Nocturnal Iris" required>
              </div>
              <div class="form-group">
                <label for="p_price">Boutique Price (₱ PHP)</label>
                <input type="number" id="p_price" name="price" step="0.01" class="form-control" placeholder="5000.00" required>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-group">
                <label for="p_subtitle">Scent Classification</label>
                <input type="text" id="p_subtitle" name="subtitle" class="form-control" placeholder="E.g. Powdered Musky Floral" required>
              </div>
              <div class="form-group">
                <label for="p_badge">Registry Badge (Optional)</label>
                <input type="text" id="p_badge" name="badge" class="form-control" placeholder="E.g. Rare Edition, Velvet Select">
              </div>
            </div>

            <div class="form-group">
              <label for="p_image">Olfactory Campaign Image URL</label>
              <input type="url" id="p_image" name="image_url" class="form-control" placeholder="https://images.unsplash.com/... or relative path" required>
            </div>

            <div class="form-group">
              <label for="p_desc">Artistic Campaign Description</label>
              <textarea id="p_desc" name="description" class="form-control" rows="4" placeholder="Craft a cinematic and sensual campaign paragraph..." required></textarea>
            </div>

            <div class="form-group-row">
              <div class="form-group">
                <label for="p_top">Top Olfactory Notes</label>
                <input type="text" id="p_top" name="top_notes" class="form-control" placeholder="E.g. Spiced Fig, Citrus Bergamot" required>
              </div>
              <div class="form-group">
                <label for="p_heart">Heart/Middle Notes</label>
                <input type="text" id="p_heart" name="heart_notes" class="form-control" placeholder="E.g. Damask Rose, Velvet Jasmine" required>
              </div>
              <div class="form-group">
                <label for="p_base">Base/Foundation Notes</label>
                <input type="text" id="p_base" name="base_notes" class="form-control" placeholder="E.g. Dark Vanilla, Ambergris, Incense" required>
              </div>
            </div>

            <div class="form-group-row" style="grid-template-columns: repeat(2, 1fr);">
              <div class="checkbox-group">
                <input type="checkbox" id="p_featured" name="is_featured" value="1">
                <label for="p_featured">Elevate to Signature Trilogy</label>
              </div>
              <div class="checkbox-group">
                <input type="checkbox" id="p_bestseller" name="is_bestseller" value="1">
                <label for="p_bestseller">Mark as Bestselling Scent</label>
              </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 30px;">Deploy Fragrance</button>
          </form>

          <div class="feedback-note" id="addProductFeedback"></div>
        </div>

        <!-- Right brief info -->
        <div>
          <div class="luxury-card" style="padding: 30px;">
            <h3 style="font-size: 1.3rem; margin-bottom: 15px; padding-bottom: 10px;">Boutique Seeding Tips</h3>
            <p style="font-size: 0.8rem; color: var(--muted-gray); line-height: 1.7; margin-bottom: 15px;">
              - Celesteà Zy follows a curated premium showcase format. Images must support premium aesthetic aspect ratios.
            </p>
            <p style="font-size: 0.8rem; color: var(--muted-gray); line-height: 1.7; margin-bottom: 15px;">
              - Slugs are compiled automatically from the Olfactory Name (e.g. <i>"Nocturnal Iris"</i> compiles into <i>"nocturnal-iris"</i>).
            </p>
            <p style="font-size: 0.8rem; color: var(--muted-gray); line-height: 1.7;">
              - Selecting <i>"Signature Trilogy"</i> registers the product directly inside the interactive 3D Tilting featured showcases.
            </p>
          </div>
        </div>

      </div>
    </div>

    <!-- PANEL: Audience Registries (Newsletter & Users) -->
    <div class="panel-content" id="panelAudience">
      
      <!-- Split Audience lists -->
      <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
        
        <!-- Newsletter Club List -->
        <div class="luxury-card" style="padding: 30px 20px;">
          <h3 style="font-size: 1.4rem; padding-bottom: 12px; margin-bottom: 20px;">The Club Subscribers (<?php echo count($subscribers); ?>)</h3>
          
          <div class="luxury-table-wrapper" style="margin-bottom: 0;">
            <table class="luxury-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Member Email Address</th>
                  <th>Join Stamp</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($subscribers)): ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: var(--muted-gray); font-style: italic; padding: 25px 0;">
                      No newsletter subscribers have requested access yet.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($subscribers as $sub): ?>
                    <tr>
                      <td style="font-family: monospace; color: var(--warm-gold); font-size: 0.8rem;">#SUB-<?php echo $sub['id']; ?></td>
                      <td style="font-weight: 400;"><?php echo htmlspecialchars($sub['email']); ?></td>
                      <td style="font-size: 0.75rem; color: var(--muted-gray);"><?php echo date('M d, Y H:i', strtotime($sub['created_at'])); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Account Registrants List -->
        <div class="luxury-card" style="padding: 30px 20px;">
          <h3 style="font-size: 1.4rem; padding-bottom: 12px; margin-bottom: 20px;">Account Registrants (<?php echo count($registeredUsers); ?>)</h3>

          <div class="luxury-table-wrapper" style="margin-bottom: 0;">
            <table class="luxury-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name & Profile</th>
                  <th>Role</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($registeredUsers)): ?>
                  <tr>
                    <td colspan="4" style="text-align: center; color: var(--muted-gray); font-style: italic; padding: 25px 0;">
                      No user accounts are registered.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($registeredUsers as $regUser): ?>
                    <tr>
                      <td style="font-family: monospace; color: var(--warm-gold); font-size: 0.8rem;">#USR-<?php echo $regUser['id']; ?></td>
                      <td>
                        <strong><?php echo htmlspecialchars($regUser['name']); ?></strong><br>
                        <span style="font-size: 0.7rem; color: var(--muted-gray);"><?php echo htmlspecialchars($regUser['email']); ?></span>
                      </td>
                      <td>
                        <span style="font-size: 0.65rem; font-weight: 500; text-transform: uppercase; color: <?php echo $regUser['role'] === 'admin' ? 'var(--warm-gold)' : 'var(--muted-gray)'; ?>">
                          <?php echo $regUser['role']; ?>
                        </span>
                      </td>
                      <td style="font-size: 0.75rem; color: var(--muted-gray);"><?php echo date('M d, Y', strtotime($regUser['created_at'])); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- ==========================================
         DANGEROUS DESTRUCTIVE SETTINGS (FACTORY RESET)
         ========================================== -->
    <div class="reset-warning-card">
      <h4><i class="fa-solid fa-triangle-exclamation"></i> Dangerous Operations: Factory Registry Reset</h4>
      <p>
        Warning: Triggering a database catalog factory reset will delete all customer orders, custom product additions, and registered subscribers. It will recreate fresh tables and seed the default luxury product portfolio and seeded users. Secure sessions will require re-authorizing.
      </p>
      
      <button class="btn-reset" id="btnResetDatabase">Reset Registry Seeds</button>
      <div class="feedback-note" id="resetFeedback" style="max-width: 500px; margin-top: 15px;"></div>
    </div>

  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Tab switching mechanics
        const tabBtns = document.querySelectorAll(".tab-btn");
        const panels = document.querySelectorAll(".panel-content");

        tabBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const targetPanel = btn.getAttribute("data-panel");

                tabBtns.forEach(b => b.classList.remove("active"));
                panels.forEach(p => p.classList.remove("active"));

                btn.classList.add("active");
                const targetContent = document.getElementById(targetPanel);
                if (targetContent) targetContent.classList.add("active");
            });
        });

        // Fulfill / Ship Order Actions
        document.querySelectorAll(".btn-fulfill").forEach(btn => {
            btn.addEventListener("click", async () => {
                const orderId = btn.getAttribute("data-id");
                btn.disabled = true;
                btn.textContent = "Processing...";

                try {
                    const response = await fetch("controllers/admin_actions.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ action: "fulfill_order", order_id: orderId })
                    });
                    const data = await response.json();

                    if (data.success) {
                        // Dynamically update UI
                        const statusPill = document.getElementById(`statusPill-${orderId}`);
                        if (statusPill) {
                            statusPill.className = "status-pill fulfilled";
                            statusPill.innerHTML = `<i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> fulfilled`;
                        }
                        
                        btn.className = "btn-action";
                        btn.disabled = true;
                        btn.textContent = "Shipped";
                        
                        alert(data.message);
                    } else {
                        btn.disabled = false;
                        btn.textContent = "Ship Order";
                        alert("Error: " + data.message);
                    }
                } catch (err) {
                    btn.disabled = false;
                    btn.textContent = "Ship Order";
                    alert("Concierge connection error. Fulfillment failed.");
                }
            });
        });

        // Add Product Form Processing
        const formAddProduct = document.getElementById("formAddProduct");
        const addProductFeedback = document.getElementById("addProductFeedback");

        if (formAddProduct) {
            formAddProduct.addEventListener("submit", async (e) => {
                e.preventDefault();
                addProductFeedback.className = "feedback-note";
                addProductFeedback.style.display = "none";

                const formData = new FormData(formAddProduct);

                try {
                    const response = await fetch("controllers/admin_actions.php", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        addProductFeedback.className = "feedback-note success";
                        addProductFeedback.textContent = data.message;
                        formAddProduct.reset();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        addProductFeedback.className = "feedback-note error";
                        addProductFeedback.textContent = data.message;
                    }
                } catch (err) {
                    addProductFeedback.className = "feedback-note error";
                    addProductFeedback.textContent = "Concierge connection error. Product creation failed.";
                }
            });
        }

        // Database Factory Re-seeding Action
        const btnResetDatabase = document.getElementById("btnResetDatabase");
        const resetFeedback = document.getElementById("resetFeedback");

        if (btnResetDatabase) {
            btnResetDatabase.addEventListener("click", async () => {
                if (!confirm("Are you absolutely sure you want to perform a factory seed reset? All current order records will be permanently lost.")) {
                    return;
                }

                btnResetDatabase.disabled = true;
                btnResetDatabase.textContent = "Wiping & Re-seeding tables...";
                resetFeedback.className = "feedback-note";
                resetFeedback.style.display = "none";

                try {
                    const response = await fetch("controllers/admin_actions.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ action: "reset_seeds" })
                    });
                    const data = await response.json();

                    if (data.success) {
                        resetFeedback.className = "feedback-note success";
                        resetFeedback.textContent = data.message;
                        setTimeout(() => {
                            window.location.href = "index.php";
                        }, 2000);
                    } else {
                        btnResetDatabase.disabled = false;
                        btnResetDatabase.textContent = "Reset Registry Seeds";
                        resetFeedback.className = "feedback-note error";
                        resetFeedback.textContent = data.message;
                    }
                } catch (err) {
                    btnResetDatabase.disabled = false;
                    btnResetDatabase.textContent = "Reset Registry Seeds";
                    resetFeedback.className = "feedback-note error";
                    resetFeedback.textContent = "Database re-seed failed. Check backend connectivity.";
                }
            });
        }
    });
  </script>
</body>
</html>
