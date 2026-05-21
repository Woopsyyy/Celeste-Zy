<?php
/**
 * Celesteà Zy - Shopping Bag Slide Drawer & Checkout Success Modal Component
 * Houses standard HTML elements for the shopping bag drawer and order confirmations.
 */
?>
<style>
    /* ==========================================
       SHOPPING BAG SIDE DRAWER (VANILLA JS)
       ========================================== */
    .cart-drawer-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(10px);
      z-index: 1001;
      opacity: 0;
      pointer-events: none;
      transition: var(--transition-smooth);
    }

    .cart-drawer-overlay.active {
      opacity: 1;
      pointer-events: all;
    }

    .cart-drawer {
      position: fixed;
      top: 0;
      right: -450px;
      width: 450px;
      max-width: 100vw;
      height: 100vh;
      background: var(--charcoal);
      border-left: 1px solid var(--border-color);
      box-shadow: -10px 0 40px rgba(0, 0, 0, 0.8);
      z-index: 1002;
      display: flex;
      flex-direction: column;
      transition: var(--transition-smooth);
    }

    .cart-drawer.active {
      right: 0;
    }

    .cart-header {
      padding: 30px 40px;
      border-bottom: 1px solid rgba(197, 168, 128, 0.15);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .cart-header h3 {
      font-size: 1.8rem;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    .cart-header h3 span {
      color: var(--warm-gold);
    }

    .btn-close-cart {
      background: none;
      border: none;
      color: var(--soft-ivory);
      font-size: 1.3rem;
      cursor: pointer;
      transition: var(--transition-smooth);
    }

    .btn-close-cart:hover {
      color: var(--warm-gold);
      transform: rotate(90deg);
    }

    .cart-items-container {
      flex-grow: 1;
      padding: 40px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    /* Cart Empty State */
    .cart-empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      height: 100%;
      gap: 20px;
      opacity: 1;
      transition: var(--transition-smooth);
    }

    .cart-empty-state.hidden {
      display: none;
    }

    .cart-empty-icon {
      font-size: 3rem;
      color: var(--border-color);
    }

    .cart-empty-state p {
      font-family: var(--font-serif);
      font-style: italic;
      color: var(--muted-gray);
      font-size: 1.15rem;
    }

    .cart-item {
      display: flex;
      gap: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      padding-bottom: 25px;
      position: relative;
    }

    .cart-item-img {
      width: 80px;
      height: 100px;
      object-fit: cover;
      border: 1px solid var(--border-color);
      background: #000;
    }

    .cart-item-details {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .cart-item-title {
      font-family: var(--font-serif);
      font-size: 1.3rem;
      color: var(--soft-ivory);
    }

    .cart-item-subtitle {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--muted-gray);
      margin-top: -3px;
    }

    .cart-item-price {
      font-family: var(--font-serif);
      font-size: 1.15rem;
      color: var(--warm-gold);
      margin-top: 5px;
    }

    .cart-item-quantity {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 10px;
    }

    .qty-btn {
      width: 24px;
      height: 24px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      background: none;
      color: var(--soft-ivory);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      transition: var(--transition-smooth);
    }

    .qty-btn:hover {
      border-color: var(--warm-gold);
      color: var(--warm-gold);
    }

    .qty-val {
      font-size: 0.85rem;
      font-family: var(--font-sans);
    }

    .btn-remove-item {
      position: absolute;
      top: 0;
      right: 0;
      background: none;
      border: none;
      color: var(--muted-gray);
      cursor: pointer;
      transition: var(--transition-smooth);
      font-size: 0.9rem;
    }

    .btn-remove-item:hover {
      color: #df4747;
    }

    .cart-footer {
      padding: 30px 40px;
      border-top: 1px solid rgba(197, 168, 128, 0.15);
      background: var(--deep-charcoal);
    }

    .cart-totals {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 25px;
    }

    .cart-total-row {
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      letter-spacing: 1px;
    }

    .cart-total-row.grand-total {
      font-family: var(--font-serif);
      font-size: 1.6rem;
      color: var(--warm-gold);
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      padding-top: 15px;
      letter-spacing: 0;
    }

    .btn-checkout {
      width: 100%;
      letter-spacing: 3px;
    }

    /* ==========================================
       CHECKOUT MODAL
       ========================================== */
    .checkout-modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      backdrop-filter: blur(15px);
      z-index: 2000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: var(--transition-smooth);
    }

    .checkout-modal-overlay.active {
      opacity: 1;
      pointer-events: all;
    }

    .checkout-modal {
      background: var(--charcoal);
      border: 1px solid var(--warm-gold);
      padding: 60px;
      max-width: 600px;
      width: 90%;
      text-align: center;
      position: relative;
      box-shadow: 0 25px 60px rgba(0,0,0,0.8);
    }

    .checkout-modal::before {
      content: '';
      position: absolute;
      inset: 6px;
      border: 1px solid rgba(197, 168, 128, 0.1);
      pointer-events: none;
    }

    .checkout-icon {
      font-size: 4rem;
      color: var(--warm-gold);
      margin-bottom: 30px;
      animation: pulseGold 2s infinite ease-in-out;
    }

    @keyframes pulseGold {
      0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(197, 168, 128, 0)); }
      50% { transform: scale(1.05); filter: drop-shadow(0 0 15px rgba(197, 168, 128, 0.4)); }
      100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(197, 168, 128, 0)); }
    }

    .checkout-title {
      font-size: 2.5rem;
      color: var(--soft-ivory);
      margin-bottom: 20px;
    }

    .checkout-msg {
      font-family: var(--font-serif);
      font-style: italic;
      color: var(--champagne-beige);
      font-size: 1.25rem;
      line-height: 1.8;
      margin-bottom: 35px;
    }

    @media (max-width: 600px) {
      .cart-drawer {
        width: 100%;
      }
    }
</style>

  <!-- ==========================================
       SHOPPING BAG SIDE DRAWER
       ========================================== -->
  <div class="cart-drawer-overlay" id="cartOverlay"></div>
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
      <h3>Your <span>Bag</span></h3>
      <button class="btn-close-cart" id="closeCart" aria-label="Close Shopping Bag"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="cart-items-container" id="cartItems">
      <!-- Empty State -->
      <div class="cart-empty-state" id="cartEmptyState">
        <div class="cart-empty-icon"><i class="fa-solid fa-basket-shopping"></i></div>
        <p>Your bag is currently empty.</p>
        <button class="btn btn-outline" id="shopEmptyBtn">Continue Browsing</button>
      </div>
    </div>

    <div class="cart-footer">
      <div class="cart-totals">
        <div class="cart-total-row">
          <span>Subtotal</span>
          <span id="cartSubtotal">₱0</span>
        </div>
        <div class="cart-total-row">
          <span>Courier Shipping (Luxury Wrap)</span>
          <span id="cartShipping">₱0</span>
        </div>
        <div class="cart-total-row grand-total">
          <span>Grand Total</span>
          <span id="cartTotal">₱0</span>
        </div>
      </div>
      <button class="btn btn-gold btn-checkout" id="checkoutBtn">Proceed To Checkout</button>
    </div>
  </div>

  <!-- ==========================================
       SIMULATED CHECKOUT MODAL (AJAX COMPLETED SUCCESS SCREEN)
       ========================================== -->
  <div class="checkout-modal-overlay" id="checkoutModalOverlay">
    <div class="checkout-modal">
      <div class="checkout-icon">
        <i class="fa-solid fa-ring"></i>
      </div>
      <h3 class="checkout-title">Order Received</h3>
      <p class="checkout-msg" id="checkoutSuccessMsg">
        Thank you for choosing Celesteà Zy. Your exclusive order is being packaged under delicate white gloves at our high-fashion boutique headquarters. A concierge tracking code has been dispatched.
      </p>
      <button class="btn btn-gold" id="closeCheckoutModal">Return to Boutique</button>
    </div>
  </div>
