<?php
/**
 * Celesteà Zy - Inner Circle Newsletter Component
 * Renders the elite invitation capture form with AJAX success overlay.
 */
?>
<style>
    /* ==========================================
       NEWSLETTER (L'ÉLITE CLUB)
       ========================================== */
    .newsletter-section {
      padding: 130px 0;
      background: linear-gradient(180deg, var(--matte-black) 0%, var(--charcoal) 100%);
      border-top: 1px solid var(--border-color);
    }

    .newsletter-wrapper {
      max-width: 850px;
      margin: 0 auto;
      background: rgba(18, 18, 20, 0.75);
      border: 1px solid var(--border-color);
      padding: 80px 60px;
      text-align: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0,0,0,0.6);
      backdrop-filter: blur(15px);
    }

    .newsletter-wrapper::before {
      content: '';
      position: absolute;
      inset: 8px;
      border: 1px solid rgba(197, 168, 128, 0.08);
      pointer-events: none;
    }

    .newsletter-icon {
      font-size: 2rem;
      color: var(--warm-gold);
      margin-bottom: 25px;
      opacity: 0.85;
    }

    .newsletter-title {
      font-size: clamp(2rem, 4vw, 3rem);
      margin-bottom: 15px;
    }

    .newsletter-desc {
      color: var(--muted-gray);
      font-size: 0.9rem;
      line-height: 1.8;
      max-width: 580px;
      margin: 0 auto 40px auto;
    }

    .newsletter-form {
      display: flex;
      justify-content: center;
      gap: 15px;
      max-width: 600px;
      margin: 0 auto;
      flex-wrap: wrap;
    }

    .newsletter-input {
      flex-grow: 1;
      min-width: 280px;
      padding: 16px 24px;
      background: rgba(8, 8, 9, 0.9);
      border: 1px solid rgba(197, 168, 128, 0.2);
      color: var(--soft-ivory);
      font-family: var(--font-sans);
      font-size: 0.85rem;
      outline: none;
      transition: var(--transition-smooth);
      letter-spacing: 1px;
    }

    .newsletter-input:focus {
      border-color: var(--warm-gold);
      box-shadow: 0 0 15px rgba(197, 168, 128, 0.1);
    }

    .newsletter-success {
      position: absolute;
      inset: 8px;
      background: var(--charcoal);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      opacity: 0;
      pointer-events: none;
      transition: var(--transition-smooth);
      z-index: 10;
    }

    .newsletter-success.active {
      opacity: 1;
      pointer-events: all;
    }

    .success-icon {
      font-size: 3rem;
      color: var(--warm-gold);
      margin-bottom: 20px;
    }

    .success-title {
      font-family: var(--font-serif);
      font-size: 2.2rem;
      color: var(--soft-ivory);
      margin-bottom: 10px;
    }

    .success-msg {
      font-family: var(--font-serif);
      font-style: italic;
      color: var(--champagne-beige);
      font-size: 1.1rem;
      text-align: center;
    }
</style>

  <!-- ==========================================
       SECTION 6: NEWSLETTER (THE INNER CIRCLE - AJAX CONNECTED)
       ========================================== -->
  <section class="newsletter-section" id="newsletter">
    <div class="container">
      <div class="newsletter-wrapper">
        <div class="newsletter-icon">
          <i class="fa-solid fa-compass"></i>
        </div>
        <span class="editorial-tag">Celesteà Club</span>
        <h2 class="luxury-heading">Join The <span class="gold-accent">Inner Circle</span></h2>
        <p class="newsletter-desc">
          Receive confidential updates, priority reservations for future celestial collection drops, and invitations to private boutique runway launches.
        </p>

        <form class="newsletter-form" id="newsForm">
          <input type="email" name="email" class="newsletter-input" placeholder="Your Email Address" required>
          <button type="submit" class="btn btn-gold">Request Invitation</button>
        </form>

        <!-- Newsletter Success Overlay -->
        <div class="newsletter-success" id="newsSuccess">
          <div class="success-icon"><i class="fa-solid fa-feather-pointed"></i></div>
          <h3 class="success-title">Your Invitation Awaits</h3>
          <p class="success-msg" id="successMsg">Welcome to Celesteà Zy. An exclusive invitation confirmation has been dispatched to your email.</p>
        </div>
      </div>
    </div>
  </section>
