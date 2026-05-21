<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Celesteà Zy | Haute Parfumerie & Cosmétique</title>
  <meta name="description" content="Immerse yourself in Celesteà Zy. A cinematic luxury fragrance house crafting unforgettable sensory campaigns. Explore exclusive perfume editorials." />

  <!-- Premium Typography and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ==========================================
       DESIGN SYSTEM & CSS VARIABLES
       ========================================== */
    :root {
      --matte-black: #080809;
      --charcoal: #121214;
      --deep-charcoal: #18181b;
      --warm-gold: #c5a880;
      --gold-bright: #d4af37;
      --champagne-beige: #ebdcb9;
      --deep-brown: #1c1510;
      --soft-ivory: #f9f8f6;
      --muted-gray: #8e8e93;
      --border-color: rgba(197, 168, 128, 0.15);
      --font-serif: 'Cormorant Garamond', serif;
      --font-sans: 'Montserrat', sans-serif;
      --transition-smooth: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Custom Selection and Scrollbar */
    ::selection {
      background: var(--warm-gold);
      color: var(--matte-black);
    }

    ::-webkit-scrollbar {
      width: 6px;
    }
    ::-webkit-scrollbar-track {
      background: var(--matte-black);
    }
    ::-webkit-scrollbar-thumb {
      background: var(--warm-gold);
      border-radius: 3px;
    }

    /* Reset Styles */
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
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* Layout Utilities */
    section {
      position: relative;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 4%;
    }

    /* Typography */
    h1, h2, h3, h4 {
      font-family: var(--font-serif);
      font-weight: 400;
      letter-spacing: 1px;
    }

    .luxury-heading {
      font-size: clamp(2.5rem, 5vw, 4.5rem);
      line-height: 1.1;
      margin-bottom: 20px;
      color: var(--soft-ivory);
      text-transform: capitalize;
    }

    .gold-accent {
      color: var(--warm-gold);
      font-style: italic;
    }

    /* ==========================================
       PREMIUM BUTTONS
       ========================================== */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 16px 36px;
      font-family: var(--font-sans);
      font-size: 0.85rem;
      font-weight: 400;
      letter-spacing: 2px;
      text-transform: uppercase;
      text-decoration: none;
      cursor: pointer;
      transition: var(--transition-smooth);
      position: relative;
      overflow: hidden;
      border: 1px solid var(--border-color);
    }

    .btn-gold {
      background: var(--warm-gold);
      color: var(--matte-black);
      border-color: var(--warm-gold);
    }

    .btn-gold:hover {
      background: transparent;
      color: var(--warm-gold);
      box-shadow: 0 10px 25px rgba(197, 168, 128, 0.15);
      transform: translateY(-2px);
    }

    .btn-outline {
      background: transparent;
      color: var(--soft-ivory);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-outline::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: 0.5s;
    }

    .btn-outline:hover::before {
      left: 100%;
    }

    .btn-outline:hover {
      border-color: var(--warm-gold);
      color: var(--warm-gold);
      transform: translateY(-2px);
    }

    /* ==========================================
       HEADER & NAVIGATION
       ========================================== */
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding: 30px 6%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
      background: linear-gradient(to bottom, rgba(8,8,9,0.9) 0%, rgba(8,8,9,0) 100%);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(197, 168, 128, 0.05);
      transition: var(--transition-smooth);
    }

    .navbar.scrolled {
      padding: 18px 6%;
      background: rgba(8, 8, 9, 0.95);
      border-bottom: 1px solid rgba(197, 168, 128, 0.15);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .logo-container {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .logo {
      font-size: 2rem;
      font-family: var(--font-serif);
      letter-spacing: 4px;
      color: var(--soft-ivory);
      text-decoration: none;
      font-weight: 300;
      text-transform: uppercase;
      transition: var(--transition-smooth);
    }

    .logo span {
      color: var(--warm-gold);
    }

    .logo-subtitle {
      font-size: 0.6rem;
      text-transform: uppercase;
      letter-spacing: 3px;
      color: var(--warm-gold);
      margin-top: -2px;
      opacity: 0.8;
    }

    .nav-links {
      display: flex;
      gap: 40px;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--soft-ivory);
      font-size: 0.78rem;
      font-weight: 400;
      letter-spacing: 2px;
      text-transform: uppercase;
      transition: var(--transition-smooth);
      position: relative;
      padding: 5px 0;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 1px;
      background: var(--warm-gold);
      transition: var(--transition-smooth);
      transform: translateX(-50%);
    }

    .nav-links a:hover {
      color: var(--warm-gold);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-icons {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .nav-icon-btn {
      background: none;
      border: none;
      color: var(--soft-ivory);
      font-size: 1.1rem;
      cursor: pointer;
      transition: var(--transition-smooth);
      position: relative;
    }

    .nav-icon-btn:hover {
      color: var(--warm-gold);
      transform: translateY(-2px);
    }

    .cart-badge {
      position: absolute;
      top: -8px;
      right: -10px;
      background: var(--warm-gold);
      color: var(--matte-black);
      font-family: var(--font-sans);
      font-size: 0.65rem;
      font-weight: 600;
      width: 17px;
      height: 17px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition-smooth);
      opacity: 0;
      transform: scale(0);
    }

    .cart-badge.active {
      opacity: 1;
      transform: scale(1);
    }

    /* Mobile Menu Toggle */
    .menu-toggle {
      display: none;
      font-size: 1.3rem;
      color: var(--soft-ivory);
      background: none;
      border: none;
      cursor: pointer;
      transition: var(--transition-smooth);
    }
  </style>
</head>
<body>

  <!-- ==========================================
       NAVIGATION BAR
       ========================================== -->
  <nav class="navbar" id="navbar">
    <div class="logo-container">
      <a href="#" class="logo">Celesteà<span>Zy</span></a>
      <span class="logo-subtitle">Haute Parfumerie</span>
    </div>

    <ul class="nav-links">
      <li><a href="#featured">Featured</a></li>
      <li><a href="#signature">Signature</a></li>
      <li><a href="#editorial">Editorial</a></li>
      <li><a href="#bestsellers">Bestsellers</a></li>
      <li><a href="#newsletter">The Club</a></li>
    </ul>

    <div class="nav-icons">
      <button class="nav-icon-btn" aria-label="Search Collection"><i class="fa-solid fa-magnifying-glass"></i></button>
      
      <button class="nav-icon-btn" id="accountTrigger" aria-label="Customer Account">
        <i class="fa-solid fa-user" <?php echo isset($_SESSION['user_id']) ? 'style="color: var(--warm-gold);"' : ''; ?>></i>
        <?php if (isset($_SESSION['user_id'])): ?>
          <span style="font-size: 0.65rem; font-family: var(--font-sans); letter-spacing: 1px; margin-left: 5px; text-transform: uppercase; font-weight: 400; color: var(--warm-gold);">
            <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
          </span>
        <?php endif; ?>
      </button>

      <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin'): ?>
        <a href="admin.php" class="nav-icon-btn" title="Admin Dashboard" style="color: var(--warm-gold); text-decoration: none; display: inline-flex; align-items: center;">
          <i class="fa-solid fa-sliders"></i>
        </a>
      <?php endif; ?>

      <button class="nav-icon-btn" id="cartTrigger" aria-label="Shopping Drawer">
        <i class="fa-solid fa-bag-shopping"></i>
        <span class="cart-badge" id="cartBadge">0</span>
      </button>
      <button class="menu-toggle" aria-label="Menu Toggle"><i class="fa-solid fa-bars"></i></button>
    </div>
  </nav>
