<?php
session_start();
/**
 * Celesteà Zy - Dynamic Luxury Perfume Showcase & Boutique
 * Fetches products dynamically from MySQL with self-install safeguards.
 * React-like component-based layout loading config and components modularly.
 */

require_once 'config/db.php';

$dbReady = false;
$featuredProducts = [];
$bestsellerProducts = [];

try {
    // Attempt database connection
    $pdo = getDBConnection(true);
    
    // Validate if products table exists and contains records
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'products'")->rowCount();
    if ($tableCheck > 0) {
        $countCheck = $pdo->query("SELECT COUNT(*) FROM `products`")->fetchColumn();
        if ($countCheck > 0) {
            $dbReady = true;
            
            // Clean up stale session if user no longer exists in DB (e.g. after setup.php reset)
            if (isset($_SESSION['user_id'])) {
                $userCheck = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `id` = ?");
                $userCheck->execute([$_SESSION['user_id']]);
                if ($userCheck->fetchColumn() == 0) {
                    $_SESSION = [];
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                    session_destroy();
                    // Restart fresh session
                    session_start();
                }
            }
            
            // Fetch Featured Fragrances (The Signature Trilogy)
            $stmt = $pdo->prepare("SELECT * FROM `products` WHERE `is_featured` = 1 ORDER BY `id` ASC");
            $stmt->execute();
            $featuredProducts = $stmt->fetchAll();
            
            // Fetch Best Sellers
            $stmt = $pdo->prepare("SELECT * FROM `products` WHERE `is_bestseller` = 1 ORDER BY `id` ASC");
            $stmt->execute();
            $bestsellerProducts = $stmt->fetchAll();
        }
    }
} catch (Exception $e) {
    // Quietly catch connection errors to present setup invitation below
}

// Redirect to self-installer if DB is uninitialized
if (!$dbReady) {
    header("Location: setup.php");
    exit;
}

// Assemble React-like page components dynamically
include_once 'pages/header.php';
include_once 'pages/hero.php';
include_once 'pages/featured.php';
include_once 'pages/signature.php';
include_once 'pages/campaign.php';
include_once 'pages/bestsellers.php';
include_once 'pages/newsletter.php';
include_once 'pages/account.php';
include_once 'pages/cart.php';
include_once 'pages/footer.php';
?>
