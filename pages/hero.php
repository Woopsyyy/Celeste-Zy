<?php
/**
 * Celesteà Zy - Hero Campaign Page Component
 * Renders the fullscreen slideshow with stable luxury assets.
 */
?>
<style>
    /* ==========================================
       CINEMATIC HERO SECTION
       ========================================== */
    .hero {
      height: 100vh;
      width: 100vw;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }

    /* Slideshow for Hero Campaign */
    .hero-slideshow {
      position: absolute;
      inset: 0;
      z-index: 1;
    }

    .hero-slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 1.8s ease-in-out, transform 12s cubic-bezier(0.25, 1, 0.50, 1);
      background-size: cover;
      background-position: center;
      transform: scale(1.1);
    }

    .hero-slide.active {
      opacity: 1;
      transform: scale(1.02);
    }

    .hero-slide::after {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle, rgba(0,0,0,0.2) 0%, rgba(8,8,9,0.85) 85%),
                  linear-gradient(to top, var(--matte-black) 0%, rgba(8,8,9,0.3) 50%, rgba(8,8,9,0.6) 100%);
    }

    /* Particle Overlay Canvas */
    #goldCanvas {
      position: absolute;
      inset: 0;
      z-index: 2;
      pointer-events: none;
      mix-blend-mode: screen;
      opacity: 0.75;
    }

    .hero-content {
      position: relative;
      z-index: 3;
      max-width: 950px;
      padding: 0 20px;
      margin-top: 50px;
    }

    .hero-sub {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 6px;
      color: var(--warm-gold);
      margin-bottom: 25px;
      font-weight: 400;
      display: block;
      animation: fadeInUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .hero-title {
      font-size: clamp(3rem, 7vw, 7.5rem);
      font-family: var(--font-serif);
      line-height: 1;
      margin-bottom: 30px;
      font-weight: 300;
      text-transform: capitalize;
      letter-spacing: 2px;
      color: var(--soft-ivory);
      animation: fadeInUp 1.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .hero-desc {
      color: rgba(249, 248, 246, 0.8);
      font-family: var(--font-serif);
      font-style: italic;
      font-size: clamp(1.1rem, 2vw, 1.6rem);
      line-height: 1.8;
      max-width: 680px;
      margin: 0 auto 45px auto;
      letter-spacing: 0.5px;
      animation: fadeInUp 2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .hero-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      animation: fadeInUp 2.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Scroll Indicator */
    .scroll-indicator {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 3;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--muted-gray);
      font-size: 0.65rem;
      letter-spacing: 3px;
      text-transform: uppercase;
      text-decoration: none;
      transition: var(--transition-smooth);
    }

    .scroll-indicator:hover {
      color: var(--warm-gold);
    }

    .scroll-line {
      width: 1px;
      height: 60px;
      background: rgba(197, 168, 128, 0.2);
      margin-top: 10px;
      position: relative;
      overflow: hidden;
    }

    .scroll-line::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 25px;
      background: var(--warm-gold);
      animation: scrollDown 2s infinite ease-in-out;
    }
</style>

  <!-- ==========================================
       CINEMATIC HERO SECTION
       ========================================== -->
  <header class="hero">
    <!-- Fading Slideshow Backdrops with Corrected Stable Image URLs -->
    <div class="hero-slideshow">
      <!-- Human Model Makeup Slide 1 (Highly stable golden beauty photo) -->
      <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=1600&auto=format&fit=crop');"></div>
      <!-- Exquisite Perfume Glass Slide 2 -->
      <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1547887537-6158d64c35b3?q=80&w=1600&auto=format&fit=crop');"></div>
      <!-- Human Model Makeup Slide 3 -->
      <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1600&auto=format&fit=crop');"></div>
    </div>

    <!-- Interactive Canvas Layer for Gold Particles -->
    <canvas id="goldCanvas"></canvas>

    <!-- Luxury Typography Overlays -->
    <div class="hero-content">
      <span class="hero-sub">La Collection Céleste</span>
      <h1 class="hero-title">Celesteà Zy</h1>
      <p class="hero-desc">“Crafted for the unforgettable.”</p>
      
      <div class="hero-buttons">
        <a href="#featured" class="btn btn-gold">Discover Collection</a>
        <a href="#editorial" class="btn btn-outline">Explore Fragrance</a>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <a href="#featured" class="scroll-indicator">
      <span>Scroll To Immerse</span>
      <div class="scroll-line"></div>
    </a>
  </header>
