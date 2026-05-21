<?php
/**
 * Celesteà Zy - Account Navigation Slide Drawer Component
 * Manages customer logins, new registrations, and visual profile/order audits.
 */

$isLoggedIn = isset($_SESSION['user_id']);
$userOrders = [];

if ($isLoggedIn) {
    try {
        $pdo = getDBConnection(true);
        // Fetch user orders and items
        $stmt = $pdo->prepare("
            SELECT o.id AS order_id, o.total_price, o.shipping_price, o.status, o.created_at,
                   oi.quantity, oi.price AS item_price, p.name AS product_name
            FROM `orders` o
            LEFT JOIN `order_items` oi ON o.id = oi.order_id
            LEFT JOIN `products` p ON oi.product_id = p.id
            WHERE o.user_id = ?
            ORDER BY o.id DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $rawOrders = $stmt->fetchAll();

        // Group items under their respective orders
        foreach ($rawOrders as $row) {
            $oid = $row['order_id'];
            if (!isset($userOrders[$oid])) {
                $userOrders[$oid] = [
                    'id' => $oid,
                    'total_price' => $row['total_price'],
                    'shipping_price' => $row['shipping_price'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'items' => []
                ];
            }
            if ($row['product_name']) {
                $userOrders[$oid]['items'][] = [
                    'name' => $row['product_name'],
                    'qty' => $row['quantity'],
                    'price' => $row['item_price']
                ];
            }
        }
    } catch (Exception $e) {
        // Fail gracefully
    }
}
?>
<style>
    /* ==========================================
       LUXURY ACCOUNT DRAWER STYLES
       ========================================== */
    .account-drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        z-index: 1101;
        opacity: 0;
        pointer-events: none;
        transition: var(--transition-smooth);
    }

    .account-drawer-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    .account-drawer {
        position: fixed;
        top: 0;
        right: -480px;
        width: 480px;
        max-width: 100vw;
        height: 100vh;
        background: var(--charcoal);
        border-left: 1px solid var(--border-color);
        box-shadow: -10px 0 40px rgba(0, 0, 0, 0.9);
        z-index: 1102;
        display: flex;
        flex-direction: column;
        transition: var(--transition-smooth);
    }

    .account-drawer.active {
        right: 0;
    }

    .account-header {
        padding: 30px 40px;
        border-bottom: 1px solid rgba(197, 168, 128, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .account-header h3 {
        font-size: 1.8rem;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .account-header h3 span {
        color: var(--warm-gold);
    }

    .btn-close-account {
        background: none;
        border: none;
        color: var(--soft-ivory);
        font-size: 1.3rem;
        cursor: pointer;
        transition: var(--transition-smooth);
    }

    .btn-close-account:hover {
        color: var(--warm-gold);
        transform: rotate(90deg);
    }

    .account-body {
        flex-grow: 1;
        padding: 40px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    /* Auth tabs switcher */
    .auth-tabs {
        display: flex;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 30px;
    }

    .auth-tab-btn {
        flex: 1;
        background: none;
        border: none;
        color: var(--muted-gray);
        font-family: var(--font-sans);
        font-size: 0.8rem;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 15px 0;
        cursor: pointer;
        transition: var(--transition-smooth);
        position: relative;
    }

    .auth-tab-btn.active {
        color: var(--warm-gold);
    }

    .auth-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--warm-gold);
    }

    .auth-tab-content {
        display: none;
    }

    .auth-tab-content.active {
        display: block;
        animation: fadeInTab 0.5s ease;
    }

    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Form Styles */
    .auth-form-group {
        margin-bottom: 22px;
        position: relative;
    }

    .auth-form-group label {
        display: block;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--muted-gray);
        margin-bottom: 8px;
    }

    .auth-form-control {
        width: 100%;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(197, 168, 128, 0.15);
        color: var(--soft-ivory);
        padding: 14px 18px;
        font-family: var(--font-sans);
        font-size: 0.85rem;
        font-weight: 300;
        transition: var(--transition-smooth);
    }

    .auth-form-control:focus {
        outline: none;
        border-color: var(--warm-gold);
        background: rgba(0, 0, 0, 0.4);
        box-shadow: 0 0 10px rgba(197, 168, 128, 0.08);
    }

    .auth-submit-btn {
        width: 100%;
        margin-top: 15px;
    }

    .auth-alert-note {
        background: rgba(197, 168, 128, 0.06);
        border: 1px solid rgba(197, 168, 128, 0.2);
        color: var(--champagne-beige);
        padding: 15px 20px;
        font-size: 0.8rem;
        line-height: 1.6;
        margin-bottom: 25px;
        border-radius: 2px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .auth-alert-note i {
        color: var(--warm-gold);
        font-size: 1rem;
        margin-top: 2px;
    }

    .auth-feedback {
        font-size: 0.8rem;
        margin-top: 15px;
        line-height: 1.5;
        min-height: 20px;
        display: none;
    }

    .auth-feedback.success {
        display: block;
        color: var(--warm-gold);
    }

    .auth-feedback.error {
        display: block;
        color: #df4747;
    }

    /* Profile Panel Styles */
    .profile-card {
        text-align: center;
        border: 1px solid rgba(197, 168, 128, 0.15);
        padding: 30px;
        background: rgba(0,0,0,0.2);
        position: relative;
        margin-bottom: 35px;
    }

    .profile-card::before {
        content: '';
        position: absolute;
        inset: 4px;
        border: 1px solid rgba(197, 168, 128, 0.05);
        pointer-events: none;
    }

    .profile-avatar {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        border: 1px solid var(--warm-gold);
        background: var(--deep-charcoal);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--warm-gold);
        margin-bottom: 15px;
    }

    .profile-name {
        font-family: var(--font-serif);
        font-size: 1.6rem;
        color: var(--soft-ivory);
        margin-bottom: 5px;
    }

    .profile-email {
        font-size: 0.8rem;
        color: var(--muted-gray);
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .profile-badge {
        display: inline-block;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 4px 10px;
        background: rgba(197, 168, 128, 0.15);
        color: var(--warm-gold);
        border: 1px solid var(--border-color);
        margin-bottom: 5px;
    }

    /* Orders List Panel */
    .orders-section-title {
        font-family: var(--font-serif);
        font-size: 1.35rem;
        color: var(--soft-ivory);
        letter-spacing: 1px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .orders-section-title span {
        font-family: var(--font-sans);
        font-size: 0.75rem;
        color: var(--warm-gold);
        border: 1px solid var(--border-color);
        padding: 2px 8px;
        border-radius: 10px;
    }

    .order-history-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .order-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(255, 255, 255, 0.01);
        padding: 20px;
        transition: var(--transition-smooth);
    }

    .order-card:hover {
        border-color: rgba(197, 168, 128, 0.25);
        background: rgba(197, 168, 128, 0.02);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 0.75rem;
        font-family: var(--font-sans);
    }

    .order-number {
        font-weight: 500;
        color: var(--soft-ivory);
    }

    .order-date {
        color: var(--muted-gray);
    }

    .order-items-summary {
        margin: 10px 0;
        padding-left: 12px;
        border-left: 1px solid var(--border-color);
        font-size: 0.8rem;
        color: var(--champagne-beige);
        line-height: 1.6;
    }

    .order-item-row {
        display: flex;
        justify-content: space-between;
    }

    .order-item-name {
        color: var(--soft-ivory);
    }

    .order-item-qty {
        color: var(--muted-gray);
        font-size: 0.75rem;
    }

    .order-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.03);
        padding-top: 10px;
    }

    .order-status {
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .order-status.pending { color: #f5a623; }
    .order-status.fulfilled { color: var(--warm-gold); }
    .order-status.cancelled { color: #df4747; }

    .order-total {
        font-family: var(--font-serif);
        font-size: 1.15rem;
        color: var(--warm-gold);
    }

    .order-empty {
        text-align: center;
        padding: 30px;
        border: 1px dashed rgba(255, 255, 255, 0.05);
        color: var(--muted-gray);
        font-family: var(--font-serif);
        font-style: italic;
        font-size: 0.95rem;
    }

    @media (max-width: 480px) {
        .account-drawer {
            width: 100%;
        }
    }
</style>

<!-- ==========================================
     CUSTOMER ACCOUNT SIDE DRAWER
     ========================================== -->
<div class="account-drawer-overlay" id="accountOverlay"></div>
<div class="account-drawer" id="accountDrawer">
    <div class="account-header">
        <h3><?php echo $isLoggedIn ? 'Votre <span>Profil</span>' : 'Mon <span>Compte</span>'; ?></h3>
        <button class="btn-close-account" id="closeAccount" aria-label="Close Account Panel"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="account-body">
        
        <?php if ($isLoggedIn): ?>
            <!-- ================= LOGGED IN PROFILE INTERFACE ================= -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <i class="fa-regular fa-star"></i>
                </div>
                <div class="profile-badge"><?php echo htmlspecialchars($_SESSION['user_role']); ?> member</div>
                <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                <button class="btn btn-outline" id="btnLogout" style="padding: 10px 24px; font-size: 0.75rem; letter-spacing: 1.5px;">Sign Out Safely</button>
                <div class="auth-feedback" id="profileFeedback"></div>
            </div>

            <!-- CUSTOMER ORDER LIST -->
            <div class="orders-section-title">
                Your Orders <span><?php echo count($userOrders); ?></span>
            </div>
            
            <div class="order-history-list">
                <?php if (empty($userOrders)): ?>
                    <div class="order-empty">
                        No orders recorded yet. Your exclusive journey begins with your first selection.
                    </div>
                <?php else: ?>
                    <?php foreach ($userOrders as $order): ?>
                        <div class="order-card">
                            <div class="order-card-header">
                                <span class="order-number">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                            </div>
                            
                            <div class="order-items-summary">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item-row">
                                        <span class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                        <span class="order-item-qty">x<?php echo $item['qty']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="order-card-footer">
                                <span class="order-status <?php echo htmlspecialchars($order['status']); ?>">
                                    <i class="fa-solid fa-circle" style="font-size: 0.45rem;"></i> <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                                <span class="order-total">₱<?php echo number_format($order['total_price'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- ================= GUEST SIGN IN / REGISTRATION INTERFACE ================= -->
            
            <!-- Auth Warning Notice for Checkout redirect triggers -->
            <div class="auth-alert-note" id="authCheckoutWarning" style="display: none;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <strong>Sign In Required</strong><br>
                    To complete your boutique checkout and preserve your customer record, please sign in or create an account.
                </div>
            </div>

            <div class="auth-tabs">
                <button class="auth-tab-btn active" data-tab="tabSignIn">Sign In</button>
                <button class="auth-tab-btn" data-tab="tabRegister">Create Account</button>
            </div>

            <!-- Tab: Sign In -->
            <div class="auth-tab-content active" id="tabSignIn">
                <form id="formSignIn" novalidate>
                    <input type="hidden" name="action" value="login">
                    
                    <div class="auth-form-group">
                        <label for="signin_email">Email or Username</label>
                        <input type="text" id="signin_email" name="email" class="auth-form-control" placeholder="admin or email@celesteazy.com" required>
                    </div>

                    <div class="auth-form-group">
                        <label for="signin_password">Password</label>
                        <input type="password" id="signin_password" name="password" class="auth-form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-gold auth-submit-btn">Authorize Account</button>
                </form>
                <div class="auth-feedback" id="signinFeedback"></div>
            </div>

            <!-- Tab: Create Account -->
            <div class="auth-tab-content" id="tabRegister">
                <form id="formRegister" novalidate>
                    <input type="hidden" name="action" value="register">

                    <div class="auth-form-group">
                        <label for="reg_name">Full Name</label>
                        <input type="text" id="reg_name" name="name" class="auth-form-control" placeholder="Lady Celeste" required>
                    </div>

                    <div class="auth-form-group">
                        <label for="reg_email">Email Address</label>
                        <input type="email" id="reg_email" name="email" class="auth-form-control" placeholder="lady@celesteazy.com" required>
                    </div>

                    <div class="auth-form-group">
                        <label for="reg_password">Password (min 6 chars)</label>
                        <input type="password" id="reg_password" name="password" class="auth-form-control" placeholder="••••••••" required>
                    </div>

                    <div class="auth-form-group">
                        <label for="reg_confirm_password">Confirm Password</label>
                        <input type="password" id="reg_confirm_password" name="confirm_password" class="auth-form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-gold auth-submit-btn">Register Account</button>
                </form>
                <div class="auth-feedback" id="registerFeedback"></div>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const accountOverlay = document.getElementById("accountOverlay");
        const accountDrawer = document.getElementById("accountDrawer");
        const accountTrigger = document.getElementById("accountTrigger");
        const closeAccount = document.getElementById("closeAccount");
        const authCheckoutWarning = document.getElementById("authCheckoutWarning");

        // Open Account Drawer
        const openAccountDrawer = (showWarning = false) => {
            if (showWarning && authCheckoutWarning) {
                authCheckoutWarning.style.display = "flex";
            } else if (authCheckoutWarning) {
                authCheckoutWarning.style.display = "none";
            }
            accountDrawer.classList.add("active");
            accountOverlay.classList.add("active");
            document.body.style.overflow = "hidden"; // Prevent scroll
        };

        // Close Account Drawer
        const closeAccountDrawer = () => {
            accountDrawer.classList.remove("active");
            accountOverlay.classList.remove("active");
            document.body.style.overflow = "auto";
        };

        if (accountTrigger) {
            accountTrigger.addEventListener("click", () => openAccountDrawer(false));
        }

        if (closeAccount) {
            closeAccount.addEventListener("click", closeAccountDrawer);
        }

        if (accountOverlay) {
            accountOverlay.addEventListener("click", closeAccountDrawer);
        }

        // Global hook to expose this function to other components (like cart.php)
        window.openAccountDrawer = openAccountDrawer;
        window.closeAccountDrawer = closeAccountDrawer;

        // Tabs Toggle Logic (Guest mode only)
        const tabBtns = document.querySelectorAll(".auth-tab-btn");
        const tabContents = document.querySelectorAll(".auth-tab-content");

        tabBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const targetTab = btn.getAttribute("data-tab");

                tabBtns.forEach(b => b.classList.remove("active"));
                tabContents.forEach(c => c.classList.remove("active"));

                btn.classList.add("active");
                const targetContent = document.getElementById(targetTab);
                if (targetContent) targetContent.classList.add("active");
            });
        });

        // Sign In Form Submission
        const formSignIn = document.getElementById("formSignIn");
        const signinFeedback = document.getElementById("signinFeedback");

        if (formSignIn) {
            formSignIn.addEventListener("submit", async (e) => {
                e.preventDefault();
                signinFeedback.className = "auth-feedback";
                signinFeedback.textContent = "Authorizing security keys...";
                signinFeedback.style.display = "block";

                const formData = new FormData(formSignIn);
                
                try {
                    const response = await fetch("controllers/auth.php", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        signinFeedback.className = "auth-feedback success";
                        signinFeedback.textContent = data.message;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        signinFeedback.className = "auth-feedback error";
                        signinFeedback.textContent = data.message;
                    }
                } catch (err) {
                    signinFeedback.className = "auth-feedback error";
                    signinFeedback.textContent = "An error occurred during authentication. Please retry.";
                }
            });
        }

        // Register Form Submission
        const formRegister = document.getElementById("formRegister");
        const registerFeedback = document.getElementById("registerFeedback");

        if (formRegister) {
            formRegister.addEventListener("submit", async (e) => {
                e.preventDefault();
                registerFeedback.className = "auth-feedback";
                registerFeedback.textContent = "Creating security profile...";
                registerFeedback.style.display = "block";

                const formData = new FormData(formRegister);

                try {
                    const response = await fetch("controllers/auth.php", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        registerFeedback.className = "auth-feedback success";
                        registerFeedback.textContent = data.message;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1200);
                    } else {
                        registerFeedback.className = "auth-feedback error";
                        registerFeedback.textContent = data.message;
                    }
                } catch (err) {
                    registerFeedback.className = "auth-feedback error";
                    registerFeedback.textContent = "An error occurred during account creation. Please retry.";
                }
            });
        }

        // Logout Processing
        const btnLogout = document.getElementById("btnLogout");
        const profileFeedback = document.getElementById("profileFeedback");

        if (btnLogout) {
            btnLogout.addEventListener("click", async () => {
                if (profileFeedback) {
                    profileFeedback.className = "auth-feedback";
                    profileFeedback.textContent = "Terminating secure session...";
                    profileFeedback.style.display = "block";
                }

                try {
                    const response = await fetch("controllers/auth.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ action: "logout" })
                    });
                    const data = await response.json();

                    if (data.success) {
                        if (profileFeedback) {
                            profileFeedback.className = "auth-feedback success";
                            profileFeedback.textContent = data.message;
                        }
                        setTimeout(() => {
                            window.location.href = "index.php";
                        }, 800);
                    }
                } catch (err) {
                    if (profileFeedback) {
                        profileFeedback.className = "auth-feedback error";
                        profileFeedback.textContent = "Logout failed. Please retry.";
                    }
                }
            });
        }
    });
</script>
