<?php
/**
 * Celesteà Zy - Best Sellers Component
 * Loops dynamically over `$bestsellerProducts` and provides QUICK ADD capabilities.
 */
?>
<style>
    /* ==========================================
       BEST SELLERS GRID
       ========================================== */
    .bestsellers-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }

    .bestseller-card {
      background: var(--charcoal);
      border: 1px solid rgba(255, 255, 255, 0.05);
      position: relative;
      overflow: hidden;
      height: 520px;
      display: flex;
      flex-direction: column;
      transition: var(--transition-smooth);
      cursor: pointer;
    }

    .bestseller-card:hover {
      border-color: var(--warm-gold);
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    }

    .bestseller-img-box {
      height: 62%;
      position: relative;
      overflow: hidden;
      background: #000;
    }

    .bestseller-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 1.2s ease;
      filter: brightness(90%);
    }

    .bestseller-card:hover img {
      transform: scale(1.05);
    }

    .bestseller-badge {
      position: absolute;
      top: 15px;
      left: 15px;
      background: rgba(8, 8, 9, 0.85);
      border: 1px solid var(--warm-gold);
      color: var(--warm-gold);
      padding: 6px 14px;
      font-size: 0.6rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      z-index: 2;
    }

    .bestseller-info {
      padding: 22px 25px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .bestseller-name {
      font-size: 1.6rem;
      color: var(--soft-ivory);
    }

    .bestseller-price {
      font-family: var(--font-serif);
      font-size: 1.25rem;
      color: var(--warm-gold);
      margin-top: 3px;
    }

    .bestseller-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
    }

    .btn-quick-add {
      background: none;
      border: none;
      color: var(--soft-ivory);
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition-smooth);
      padding: 5px 0;
      position: relative;
    }

    .btn-quick-add::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 1px;
      background: var(--warm-gold);
      transition: var(--transition-smooth);
    }

    .bestseller-card:hover .btn-quick-add {
      color: var(--warm-gold);
    }

    .bestseller-card:hover .btn-quick-add::after {
      width: 100%;
    }
</style>

  <!-- ==========================================
       SECTION 5: BEST SELLERS (DYNAMIC DATABASE LOOP)
       ========================================== -->
  <section class="section-editorial" id="bestsellers">
    <div class="container">
      <div class="editorial-header">
        <span class="editorial-tag">The Boutique</span>
        <h2 class="luxury-heading">Iconic <span class="gold-accent">Sellers</span></h2>
        <p class="editorial-subtitle">Curated masterpieces highly sought after by fragrance connoisseurs around the globe.</p>
      </div>

      <div class="bestsellers-grid">
        <?php foreach ($bestsellerProducts as $product): ?>
          <div class="bestseller-card" 
               data-name="<?php echo htmlspecialchars($product['name']); ?>" 
               data-price="<?php echo htmlspecialchars($product['price']); ?>" 
               data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
            <div class="bestseller-img-box">
              <?php if (!empty($product['badge'])): ?>
                <span class="bestseller-badge"><?php echo htmlspecialchars($product['badge']); ?></span>
              <?php endif; ?>
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> Scent Vessel">
            </div>
            <div class="bestseller-info">
              <div>
                <h3 class="bestseller-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="bestseller-price">₱<?php echo number_format($product['price']); ?></p>
              </div>
              <div class="bestseller-actions">
                <button class="btn-quick-add">Quick Add <i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
