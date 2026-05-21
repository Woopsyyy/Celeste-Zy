<?php
/**
 * Celesteà Zy - Global Footer & JavaScript Interactions Component
 * Renders the luxury footer links and drives the cinematic, parallax, and AJAX mechanics.
 */
?>
<style>
    /* ==========================================
       FOOTER
       ========================================== */
    footer {
      background: var(--matte-black);
      border-top: 1px solid rgba(197, 168, 128, 0.08);
      padding: 90px 6% 40px 6%;
      position: relative;
      z-index: 10;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 60px;
      margin-bottom: 80px;
    }

    .footer-brand {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .footer-logo {
      font-size: 2rem;
      font-family: var(--font-serif);
      letter-spacing: 4px;
      color: var(--soft-ivory);
      text-decoration: none;
      text-transform: uppercase;
    }

    .footer-logo span {
      color: var(--warm-gold);
    }

    .footer-desc {
      color: var(--muted-gray);
      font-size: 0.85rem;
      line-height: 1.8;
      max-width: 320px;
    }

    .footer-socials {
      display: flex;
      gap: 15px;
    }

    .social-link {
      width: 38px;
      height: 38px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--soft-ivory);
      text-decoration: none;
      transition: var(--transition-smooth);
    }

    .social-link:hover {
      border-color: var(--warm-gold);
      color: var(--warm-gold);
      transform: translateY(-3px);
    }

    .footer-col h4 {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--warm-gold);
      margin-bottom: 25px;
      font-weight: 500;
    }

    .footer-links {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 15px;
      padding: 0;
    }

    .footer-links a {
      color: var(--muted-gray);
      text-decoration: none;
      font-size: 0.82rem;
      transition: var(--transition-smooth);
      display: inline-block;
    }

    .footer-links a:hover {
      color: var(--soft-ivory);
      transform: translateX(4px);
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      padding-top: 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .copyright {
      color: var(--muted-gray);
      font-size: 0.78rem;
    }

    .footer-legal {
      display: flex;
      gap: 25px;
    }

    .footer-legal a {
      color: var(--muted-gray);
      text-decoration: none;
      font-size: 0.78rem;
      transition: var(--transition-smooth);
    }

    .footer-legal a:hover {
      color: var(--warm-gold);
    }

    @media (max-width: 900px) {
      .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
      }
    }

    @media (max-width: 600px) {
      .footer-grid {
        grid-template-columns: 1fr;
      }
    }
</style>

  <!-- ==========================================
       FOOTER
       ========================================== -->
  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="#" class="footer-logo">Celesteà<span>Zy</span></a>
          <p class="footer-desc">
            An elite house of high luxury cosmetics and fragrances inspired by the dark mystery of the moon.
          </p>
          <div class="footer-socials">
            <a href="#" class="social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="social-link" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
            <a href="#" class="social-link" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="social-link" aria-label="Youtube"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>

        <div class="footer-col">
          <h4>The House</h4>
          <ul class="footer-links">
            <li><a href="#">About Us</a></li>
            <li><a href="#">La Maison</a></li>
            <li><a href="#">Sustainability</a></li>
            <li><a href="#">Careers</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Collections</h4>
          <ul class="footer-links">
            <li><a href="#">Noir Collection</a></li>
            <li><a href="#">Céleste Extraction</a></li>
            <li><a href="#">Limited Releases</a></li>
            <li><a href="#">Cosmétique Sets</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Boutique Concierge</h4>
          <ul class="footer-links">
            <li><a href="#">Contact Concierge</a></li>
            <li><a href="#">Boutique Locator</a></li>
            <li><a href="#">Shipping & Returns</a></li>
            <li><a href="#">Fragrance Profiling</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p class="copyright">© 2026 Celesteà Zy Inc. — Designed by Haute Parfumerie Creative.</p>
        <div class="footer-legal">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms & Conditions</a>
          <a href="#">Accessibility</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- ==========================================
       VANILLA JAVASCRIPT MECHANICS
       ========================================== -->
  <script>
    /* ==========================================
       1. FIXED NAVBAR DYNAMICS & SMOOTH SCROLLING
       ========================================== */
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    /* ==========================================
       2. CINEMATIC HERO SLIDESHOW CROSS-FADE
       ========================================== */
    const slides = document.querySelectorAll('.hero-slide');
    let currentSlide = 0;

    function nextSlide() {
      if (slides.length > 0) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
      }
    }
    setInterval(nextSlide, 5000);

    /* ==========================================
       3. HTML5 CANVAS: FLOATING SHIMMERING GOLD PARTICLES
       ========================================== */
    const canvas = document.getElementById('goldCanvas');
    if (canvas) {
      const ctx = canvas.getContext('2d');
      let particles = [];
      const maxParticles = 100;

      function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      }
      resizeCanvas();
      window.addEventListener('resize', resizeCanvas);

      class GoldParticle {
        constructor() {
          this.reset();
        }

        reset() {
          this.x = Math.random() * canvas.width;
          this.y = canvas.height + Math.random() * 20;
          this.size = Math.random() * 2 + 0.8;
          this.speedX = Math.random() * 0.4 - 0.2;
          this.speedY = -(Math.random() * 0.7 + 0.3);
          this.alpha = Math.random() * 0.7 + 0.3;
          this.fadeSpeed = Math.random() * 0.005 + 0.002;
          this.angle = Math.random() * Math.PI * 2;
          this.angleSpeed = Math.random() * 0.02 - 0.01;
        }

        update() {
          this.y += this.speedY;
          this.x += this.speedX + Math.sin(this.angle) * 0.15;
          this.angle += this.angleSpeed;
          this.alpha -= this.fadeSpeed;

          if (this.y < 0 || this.alpha <= 0) {
            this.reset();
          }
        }

        draw() {
          ctx.beginPath();
          ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(197, 168, 128, ${this.alpha})`;
          ctx.shadowBlur = 8;
          ctx.shadowColor = 'rgba(212, 175, 55, 0.4)';
          ctx.fill();
          ctx.shadowBlur = 0;
        }
      }

      for (let i = 0; i < maxParticles; i++) {
        particles.push(new GoldParticle());
        particles[i].y = Math.random() * canvas.height;
      }

      function animateParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
          p.update();
          p.draw();
        });
        requestAnimationFrame(animateParticles);
      }
      animateParticles();
    }

    /* ==========================================
       4. 3D PARALLAX INTERACTIVE CARD HOVER TILT
       ========================================== */
    const cards = document.querySelectorAll('.fragrance-card');
    cards.forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotX = (centerY - y) / 20;
        const rotY = (x - centerX) / 20;

        card.style.transform = `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-8px)`;
      });

      card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
      });
    });

    /* ==========================================
       5. EDITORIAL CAMPAIGN INGREDIENTS INTERACTION
       ========================================== */
    const ingTags = document.querySelectorAll('.ingredient-tag');
    const ingDescBox = document.getElementById('ingredientDesc');

    ingTags.forEach(tag => {
      tag.addEventListener('click', function() {
        ingTags.forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const newDesc = this.getAttribute('data-desc');
        ingDescBox.classList.remove('active');
        setTimeout(() => {
          ingDescBox.textContent = newDesc;
          ingDescBox.classList.add('active');
        }, 300);
      });
    });

    /* ==========================================
       6. SHOPPING BAG & CART SYSTEM (VANILLA JS + FULLSTACK CONNECTED)
       ========================================== */
    let cart = [];

    const cartDrawer = document.getElementById('cartDrawer');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartTrigger = document.getElementById('cartTrigger');
    const closeCart = document.getElementById('closeCart');
    const cartBadge = document.getElementById('cartBadge');
    const cartItems = document.getElementById('cartItems');
    const cartEmptyState = document.getElementById('cartEmptyState');
    const shopEmptyBtn = document.getElementById('shopEmptyBtn');
    
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartShipping = document.getElementById('cartShipping');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');

    const checkoutModalOverlay = document.getElementById('checkoutModalOverlay');
    const closeCheckoutModal = document.getElementById('closeCheckoutModal');
    const checkoutSuccessMsg = document.getElementById('checkoutSuccessMsg');

    function openCartDrawer() {
      cartDrawer.classList.add('active');
      cartOverlay.classList.add('active');
    }

    function closeCartDrawer() {
      cartDrawer.classList.remove('active');
      cartOverlay.classList.remove('active');
    }

    if (cartTrigger) cartTrigger.addEventListener('click', openCartDrawer);
    if (closeCart) closeCart.addEventListener('click', closeCartDrawer);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCartDrawer);
    if (shopEmptyBtn) shopEmptyBtn.addEventListener('click', closeCartDrawer);

    function formatCurrency(num) {
      return '₱' + num.toLocaleString();
    }

    function updateCartSummary() {
      let totalCount = 0;
      let subPrice = 0;

      cart.forEach(item => {
        totalCount += item.quantity;
        subPrice += item.price * item.quantity;
      });

      if (totalCount > 0) {
        cartBadge.textContent = totalCount;
        cartBadge.classList.add('active');
        cartEmptyState.classList.add('hidden');
      } else {
        cartBadge.classList.remove('active');
        cartEmptyState.classList.remove('hidden');
      }

      let shippingPrice = subPrice > 0 ? 250 : 0;

      cartSubtotal.textContent = formatCurrency(subPrice);
      cartShipping.textContent = shippingPrice > 0 ? formatCurrency(shippingPrice) : '₱0';
      cartTotal.textContent = formatCurrency(subPrice + shippingPrice);
    }

    function renderCartItems() {
      const existingItems = cartItems.querySelectorAll('.cart-item');
      existingItems.forEach(item => item.remove());

      if (cart.length === 0) {
        return;
      }

      cart.forEach(item => {
        const itemNode = document.createElement('div');
        itemNode.className = 'cart-item';
        itemNode.innerHTML = `
          <img src="${item.image}" class="cart-item-img" alt="${item.name}">
          <div class="cart-item-details">
            <div>
              <h4 class="cart-item-title">${item.name}</h4>
              <span class="cart-item-subtitle">Celesteà Zy Parfum</span>
              <p class="cart-item-price">${formatCurrency(item.price)}</p>
            </div>
            <div class="cart-item-quantity">
              <button class="qty-btn dec-qty" data-name="${item.name}"><i class="fa-solid fa-minus"></i></button>
              <span class="qty-val">${item.quantity}</span>
              <button class="qty-btn inc-qty" data-name="${item.name}"><i class="fa-solid fa-plus"></i></button>
            </div>
          </div>
          <button class="btn-remove-item" data-name="${item.name}" aria-label="Remove Item"><i class="fa-regular fa-trash-can"></i></button>
        `;
        cartItems.appendChild(itemNode);
      });

      document.querySelectorAll('.dec-qty').forEach(btn => {
        btn.addEventListener('click', () => changeQuantity(btn.dataset.name, -1));
      });
      document.querySelectorAll('.inc-qty').forEach(btn => {
        btn.addEventListener('click', () => changeQuantity(btn.dataset.name, 1));
      });
      document.querySelectorAll('.btn-remove-item').forEach(btn => {
        btn.addEventListener('click', () => removeItem(btn.dataset.name));
      });
    }

    function addToCart(name, price, image) {
      const existing = cart.find(item => item.name === name);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push({ name, price, image, quantity: 1 });
      }
      
      renderCartItems();
      updateCartSummary();
      openCartDrawer();
    }

    function changeQuantity(name, amount) {
      const item = cart.find(item => item.name === name);
      if (item) {
        item.quantity += amount;
        if (item.quantity <= 0) {
          removeItem(name);
          return;
        }
        renderCartItems();
        updateCartSummary();
      }
    }

    function removeItem(name) {
      cart = cart.filter(item => item.name !== name);
      renderCartItems();
      updateCartSummary();
    }

    // Bind to featured product card actions
    document.querySelectorAll('.fragrance-card').forEach(card => {
      card.addEventListener('click', (e) => {
        if (e.target.closest('.shop-now-link') || e.target.closest('.scent-overlay') === null) {
          const name = card.dataset.name;
          const price = parseFloat(card.dataset.price);
          const image = card.dataset.image;
          addToCart(name, price, image);
        }
      });
    });

    // Bind to best seller Quick Add actions
    document.querySelectorAll('.bestseller-card').forEach(card => {
      card.addEventListener('click', (e) => {
        const name = card.dataset.name;
        const price = parseFloat(card.dataset.price);
        const image = card.dataset.image;
        addToCart(name, price, image);
      });
    });

    /* ==========================================
       7. NEWSLETTER FORM (FULLSTACK ASYNC AJAX CALL)
       ========================================== */
    const newsForm = document.getElementById('newsForm');
    const newsSuccess = document.getElementById('newsSuccess');
    const successMsg = document.getElementById('successMsg');

    if (newsForm) {
      newsForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(newsForm);
        
        // Async POST request to controllers/subscribe.php
        fetch('controllers/subscribe.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            successMsg.textContent = data.message;
            newsSuccess.classList.add('active');
            
            setTimeout(() => {
              newsSuccess.classList.remove('active');
              newsForm.reset();
            }, 6000);
          } else {
            alert("Concierge system report: " + (data.error || "An error occurred."));
          }
        })
        .catch(err => {
            console.error("AJAX Error: ", err);
            alert("Unable to reach the boutique subscription server.");
        });
      });
    }

    /* ==========================================
       8. CHECKOUT EXPERIENCE (FULLSTACK ASYNC MYSQL TRANSACTION)
       ========================================== */
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        if (cart.length === 0) {
          alert("Your shopping bag is empty. Explore our exquisite fragrances to continue.");
          return;
        }

        checkoutBtn.disabled = true;
        checkoutBtn.textContent = "Packaging Luxury Order...";

        // Async POST request to controllers/checkout.php with cart payload
        fetch('controllers/checkout.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ items: cart })
        })
        .then(response => {
          if (response.status === 401) {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = "Proceed To Checkout";
            closeCartDrawer();
            setTimeout(() => {
              if (window.openAccountDrawer) {
                window.openAccountDrawer(true);
              } else {
                alert("Please sign in or register to complete your checkout.");
              }
            }, 350);
            throw new Error("unauthorized_checkout");
          }
          return response.json();
        })
        .then(data => {
          checkoutBtn.disabled = false;
          checkoutBtn.textContent = "Proceed To Checkout";
          
          if (data.success) {
            // Update modal content with dynamic Order ID
            checkoutSuccessMsg.innerHTML = `
              Thank you for choosing Celesteà Zy. Your exclusive order (<strong>Reference Code: #CZ-260${data.order_id}</strong>) is being packaged under delicate white gloves at our high-fashion boutique headquarters. A concierge tracking code has been dispatched.
            `;
            
            // Close drawer and open success checkout modal
            closeCartDrawer();
            setTimeout(() => {
              checkoutModalOverlay.classList.add('active');
              // Reset local cart state
              cart = [];
              renderCartItems();
              updateCartSummary();
            }, 350);
          } else {
            alert("Checkout Concierge Error: " + (data.error || "Could not register order."));
          }
        })
        .catch(err => {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = "Proceed To Checkout";
            if (err.message !== "unauthorized_checkout") {
                console.error("Checkout AJAX Error: ", err);
                alert("Connection to the checkout processing server failed.");
            }
        });
      });
    }

    if (closeCheckoutModal) {
      closeCheckoutModal.addEventListener('click', () => {
        checkoutModalOverlay.classList.remove('active');
        window.location.reload();
      });
    }

    if (checkoutModalOverlay) {
      checkoutModalOverlay.addEventListener('click', (e) => {
        if (e.target === checkoutModalOverlay) {
          checkoutModalOverlay.classList.remove('active');
          window.location.reload();
        }
      });
    }
  </script>
</body>
</html>
