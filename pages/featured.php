<?php
/**
 * Celesteà Zy - Featured Fragrances Component (The Signature Trilogy)
 * Dynamic trilogy loop using database variables.
 */
?>
<style>
    /* ==========================================
       FEATURED FRAGRANCES (3D PARALLAX CARDS)
       ========================================== */
    .section-editorial {
      padding: 140px 0;
      border-bottom: 1px solid rgba(197, 168, 128, 0.05);
    }

    .editorial-header {
      text-align: center;
      max-width: 800px;
      margin: 0 auto 90px auto;
    }

    .editorial-tag {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 5px;
      color: var(--warm-gold);
      display: block;
      margin-bottom: 15px;
      font-weight: 500;
    }

    .editorial-subtitle {
      font-size: 1.1rem;
      color: var(--muted-gray);
      margin-top: 20px;
      font-weight: 300;
      letter-spacing: 0.5px;
    }

    .fragrance-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
    }

    .fragrance-card {
      background: var(--charcoal);
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 620px;
      transition: var(--transition-smooth);
      cursor: pointer;
      perspective: 1000px;
    }

    .fragrance-card::before {
      content: '';
      position: absolute;
      inset: 0;
      border: 1px solid transparent;
      transition: var(--transition-smooth);
      pointer-events: none;
      z-index: 10;
    }

    .fragrance-card:hover::before {
      border-color: rgba(197, 168, 128, 0.4);
      inset: 15px;
    }

    .img-container {
      height: 65%;
      width: 100%;
      position: relative;
      overflow: hidden;
      background: #000;
    }

    .fragrance-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
      filter: grayscale(15%) brightness(95%);
    }

    .fragrance-card:hover img {
      transform: scale(1.08);
      filter: grayscale(0%) brightness(100%);
    }

    /* Scent Notes Pyramids - Revealed on Hover */
    .scent-overlay {
      position: absolute;
      inset: 0;
      background: rgba(8, 8, 9, 0.85);
      backdrop-filter: blur(8px);
      opacity: 0;
      transition: var(--transition-smooth);
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 40px;
      z-index: 5;
    }

    .fragrance-card:hover .scent-overlay {
      opacity: 1;
    }

    .scent-overlay h4 {
      font-size: 1.6rem;
      color: var(--warm-gold);
      margin-bottom: 25px;
      border-bottom: 1px solid rgba(197, 168, 128, 0.2);
      padding-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .scent-notes-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .scent-note-item {
      display: flex;
      flex-direction: column;
    }

    .scent-note-label {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--warm-gold);
      font-weight: 500;
      margin-bottom: 2px;
    }

    .scent-note-value {
      font-size: 0.88rem;
      color: var(--soft-ivory);
    }

    .fragrance-info {
      padding: 30px 35px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      z-index: 4;
      background: var(--charcoal);
      transition: var(--transition-smooth);
    }

    .fragrance-card:hover .fragrance-info {
      background: var(--deep-charcoal);
    }

    .card-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 8px;
    }

    .card-title {
      font-size: 2rem;
      color: var(--soft-ivory);
      text-transform: capitalize;
      letter-spacing: 1px;
    }

    .card-subtitle {
      font-size: 0.75rem;
      color: var(--muted-gray);
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .card-price {
      font-family: var(--font-serif);
      font-size: 1.4rem;
      color: var(--warm-gold);
      font-weight: 400;
    }

    .card-desc {
      font-size: 0.8rem;
      color: rgba(249, 248, 246, 0.7);
      line-height: 1.6;
      margin-top: 8px;
      margin-bottom: 20px;
    }

    .card-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: auto;
    }

    .shop-now-link {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--soft-ivory);
      text-decoration: none;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: var(--transition-smooth);
    }

    .shop-now-link i {
      font-size: 0.8rem;
      transition: var(--transition-smooth);
    }

    .fragrance-card:hover .shop-now-link {
      color: var(--warm-gold);
    }

    .fragrance-card:hover .shop-now-link i {
      transform: translateX(6px);
      color: var(--warm-gold);
    }
</style>

  <!-- ==========================================
       SECTION 2: FEATURED FRAGRANCES (DYNAMIC DATABASE LOOP)
       ========================================== -->
  <section class="section-editorial" id="featured">
    <div class="container">
      <div class="editorial-header">
        <span class="editorial-tag">Exclusives</span>
        <h2 class="luxury-heading">The Signature <span class="gold-accent">Trilogy</span></h2>
        <p class="editorial-subtitle">Each fragrance tells a story of celestial beauty, sensory romance, and exquisite craftsmanship.</p>
      </div>

      <div class="fragrance-grid">
        <?php foreach ($featuredProducts as $product): ?>
          <div class="fragrance-card" 
               data-name="<?php echo htmlspecialchars($product['name']); ?>" 
               data-price="<?php echo htmlspecialchars($product['price']); ?>" 
               data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
            <div class="img-container">
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> Premium Perfume Bottle">
              
              <!-- Hover Note Explorer dynamically generated from MySQL notes columns -->
              <div class="scent-overlay">
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <ul class="scent-notes-list">
                  <li class="scent-note-item">
                    <span class="scent-note-label">Top Note</span>
                    <span class="scent-note-value"><?php echo htmlspecialchars($product['top_notes']); ?></span>
                  </li>
                  <li class="scent-note-item">
                    <span class="scent-note-label">Heart Note</span>
                    <span class="scent-note-value"><?php echo htmlspecialchars($product['heart_notes']); ?></span>
                  </li>
                  <li class="scent-note-item">
                    <span class="scent-note-label">Base Note</span>
                    <span class="scent-note-value"><?php echo htmlspecialchars($product['base_notes']); ?></span>
                  </li>
                </ul>
              </div>
            </div>

            <div class="fragrance-info">
              <div>
                <div class="card-top">
                  <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                  <span class="card-price">₱<?php echo number_format($product['price']); ?></span>
                </div>
                <span class="card-subtitle"><?php echo htmlspecialchars($product['subtitle']); ?></span>
                <p class="card-desc"><?php echo htmlspecialchars($product['description']); ?></p>
              </div>
              
              <div class="card-actions">
                <span class="shop-now-link">Add To Bag <i class="fa-solid fa-arrow-right"></i></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
