<?php
/**
 * Celesteà Zy - Signature Extraction Editorial Component
 * Alternating asymmetrical story blocks explaining fragrance creation.
 */
?>
<style>
    /* ==========================================
       SIGNATURE COLLECTION (EDITORIAL LAYOUT)
       ========================================== */
    .signature-row {
      display: flex;
      align-items: center;
      gap: 8%;
      margin-bottom: 120px;
    }

    .signature-row:nth-child(even) {
      flex-direction: row-reverse;
    }

    .signature-media {
      flex: 1;
      height: 650px;
      position: relative;
      overflow: hidden;
      border: 1px solid var(--border-color);
    }

    .signature-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(80%) contrast(105%);
      transition: transform 10s ease;
    }

    .signature-media:hover img {
      transform: scale(1.05);
    }

    .signature-content {
      flex: 1;
      padding: 40px 0;
    }

    .signature-num {
      font-family: var(--font-serif);
      font-size: 1.5rem;
      color: var(--warm-gold);
      display: block;
      margin-bottom: 20px;
      opacity: 0.7;
    }

    .signature-title {
      font-size: clamp(2rem, 4vw, 3.5rem);
      line-height: 1.1;
      margin-bottom: 25px;
    }

    .signature-story {
      color: var(--muted-gray);
      font-size: 0.95rem;
      line-height: 1.9;
      margin-bottom: 35px;
    }

    .signature-meta {
      display: flex;
      gap: 40px;
      border-top: 1px solid rgba(197, 168, 128, 0.15);
      padding-top: 30px;
    }

    .meta-box h5 {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--warm-gold);
      margin-bottom: 8px;
    }

    .meta-box p {
      font-family: var(--font-serif);
      font-size: 1.1rem;
      color: var(--soft-ivory);
      font-style: italic;
    }
</style>

  <!-- ==========================================
       SECTION 3: SIGNATURE COLLECTION (EDITORIAL LAYOUT)
       ========================================== -->
  <section class="section-editorial" id="signature" style="background: var(--charcoal);">
    <div class="container">
      <div class="editorial-header">
        <span class="editorial-tag">La Maison</span>
        <h2 class="luxury-heading">The Art of <span class="gold-accent">Extraction</span></h2>
        <p class="editorial-subtitle">Crafting the ethereal. Each droplet represents years of curation and luxury heritage.</p>
      </div>

      <!-- Editorial Row 1 -->
      <div class="signature-row">
        <div class="signature-media">
          <img src="https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?q=80&w=1200&auto=format&fit=crop" alt="Liquid Gold Shimmer Details">
        </div>
        <div class="signature-content">
          <span class="signature-num">01 / L'Obsidienne</span>
          <h3 class="signature-title">Crafting the Mysterious Shadow</h3>
          <p class="signature-story">
            We scour the globe to extract only the most exotic elements. Our dark ambergris is sourced from remote volcanic coasts, while our dark orchid is grown under strict celestial cycles in custom temperature glasshouses. This process imbues our formulations with unmatched longevity and a sensual, lingering trace that captivates.
          </p>
          <div class="signature-meta">
            <div class="meta-box">
              <h5>Origins</h5>
              <p>Madagascar & Grasse</p>
            </div>
            <div class="meta-box">
              <h5>Aura</h5>
              <p>Sensual, Enigmatic</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Editorial Row 2 -->
      <div class="signature-row">
        <div class="signature-media">
          <img src="https://images.unsplash.com/photo-1585218356057-dc0e8d3558bb?q=80&w=1200&auto=format&fit=crop" alt="Amber Perfume Bottle close up">
        </div>
        <div class="signature-content">
          <span class="signature-num">02 / L'Alchimie</span>
          <h3 class="signature-title">The Alchemy of Velvet Gold</h3>
          <p class="signature-story">
            Our gold infusions are suspended within heavy glass bottles crafted by multi-generational master blowers. By utilizing modern double-distillation columns alongside ancient cold-press methods, we retain the absolute purest volatile compound footprint, leaving an unforgettable, cinematic impression upon the skin.
          </p>
          <div class="signature-meta">
            <div class="meta-box">
              <h5>Distillation</h5>
              <p>Double-Vaporization</p>
            </div>
            <div class="meta-box">
              <h5>Vessel</h5>
              <p>Blow Glass Gold Ring</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
