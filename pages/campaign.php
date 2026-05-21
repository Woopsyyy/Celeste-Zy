<?php
/**
 * Celesteà Zy - Editorial Campaign Banner & Ingredients Explorer Component
 * Utilizes stable background asset and interactive list selectors.
 */
?>
<style>
    /* ==========================================
       EDITORIAL CAMPAIGN (THE FRAGRANCE FILM STILL)
       ========================================== */
    .campaign-banner {
      height: 80vh;
      min-height: 600px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
                  url('https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=1600&auto=format&fit=crop') center/cover;
      background-attachment: fixed;
      text-align: center;
      border-top: 1px solid var(--border-color);
      border-bottom: 1px solid var(--border-color);
    }

    .campaign-content {
      z-index: 5;
      max-width: 900px;
      padding: 0 4%;
    }

    .campaign-quote {
      font-family: var(--font-serif);
      font-size: clamp(2rem, 5vw, 4.5rem);
      line-height: 1.2;
      color: var(--soft-ivory);
      margin-bottom: 30px;
      font-style: italic;
      font-weight: 300;
    }

    .campaign-author {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 4px;
      color: var(--warm-gold);
      margin-bottom: 40px;
      display: block;
    }

    /* Campaign Ingredients Explorer */
    .ingredients-explorer {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .ingredient-tag {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(197, 168, 128, 0.2);
      padding: 10px 24px;
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      border-radius: 30px;
      cursor: pointer;
      color: var(--soft-ivory);
      transition: var(--transition-smooth);
    }

    .ingredient-tag:hover, .ingredient-tag.active {
      background: var(--warm-gold);
      color: var(--matte-black);
      border-color: var(--warm-gold);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(197, 168, 128, 0.2);
    }

    .ingredient-detail-box {
      max-width: 500px;
      margin: 30px auto 0 auto;
      min-height: 60px;
      font-family: var(--font-serif);
      font-style: italic;
      font-size: 1.15rem;
      color: var(--champagne-beige);
      transition: var(--transition-smooth);
      opacity: 0;
      transform: translateY(10px);
    }

    .ingredient-detail-box.active {
      opacity: 1;
      transform: translateY(0);
    }
</style>

  <!-- ==========================================
       SECTION 4: EDITORIAL CAMPAIGN
       ========================================== -->
  <section class="campaign-banner" id="editorial">
    <div class="campaign-content">
      <h2 class="campaign-quote">“A whisper of stardust.<br>A presence of gold.”</h2>
      <span class="campaign-author">— Celesteà Zy Editorial Campaign</span>
      
      <!-- Interactive Ingredient Detail Panel -->
      <div class="ingredients-explorer">
        <button class="ingredient-tag active" data-desc="Extracted only during the peak moon phase, giving an incredibly deep, warm earthy note that forms a signature lunar base.">Midnight Ambergris</button>
        <button class="ingredient-tag" data-desc="Rare hand-picked saffron filaments dried over smoke, adding a dry, leathery, gold-colored initial spark.">Gold Saffron</button>
        <button class="ingredient-tag" data-desc="A highly sensual, velvety crimson rose petals essence cultivated exclusively in French organic valley farms.">Crimson Rose</button>
      </div>

      <div class="ingredient-detail-box active" id="ingredientDesc">
        Extracted only during the peak moon phase, giving an incredibly deep, warm earthy note that forms a signature lunar base.
      </div>
    </div>
  </section>
