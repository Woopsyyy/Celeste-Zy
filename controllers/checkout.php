<?php
/**
 * Celesteà Zy - Checkout Processing Endpoint (Async AJAX Controller)
 * Manages cart orders under transaction blocks for integrity.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Please sign in or register to complete your checkout.']);
    exit;
}

$userId = $_SESSION['user_id'];

$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);

if (!$data || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'error' => 'Your shopping bag appears to be empty.']);
    exit;
}

// Include db.php from config folder
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDBConnection(true);
    
    // Validate that the session user still exists in the database to prevent stale integrity failures
    $userCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `id` = ?");
    $userCheckStmt->execute([$userId]);
    if ($userCheckStmt->fetchColumn() == 0) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'error' => 'Your secure session has expired or your profile is no longer registered. Please sign in again.'
        ]);
        exit;
    }
    
    // Start atomic transaction
    $pdo->beginTransaction();
    
    $items = $data['items'];
    $subtotal = 0;
    
    $validatedItems = [];
    foreach ($items as $item) {
        $name = isset($item['name']) ? trim($item['name']) : '';
        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        
        if (empty($name) || $qty <= 0) {
            continue;
        }
        
        $stmt = $pdo->prepare("SELECT `id`, `name`, `price` FROM `products` WHERE `name` = ?");
        $stmt->execute([$name]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception("Product '{$name}' is not registered in our collections.");
        }
        
        $validatedItems[] = [
            'id' => $product['id'],
            'price' => $product['price'],
            'quantity' => $qty
        ];
        
        $subtotal += $product['price'] * $qty;
    }
    
    if (empty($validatedItems)) {
        throw new Exception("No valid fragrances were found in your shopping bag.");
    }
    
    $shipping = $subtotal > 0 ? 250.00 : 0;
    $grandTotal = $subtotal + $shipping;
    
    // 1. Insert into orders table
    $orderStmt = $pdo->prepare("INSERT INTO `orders` (`user_id`, `total_price`, `shipping_price`, `status`) VALUES (?, ?, ?, 'pending')");
    $orderStmt->execute([$userId, $grandTotal, $shipping]);
    $orderId = $pdo->lastInsertId();
    
    // 2. Insert into order_items table
    $itemStmt = $pdo->prepare("INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)");
    foreach ($validatedItems as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for choosing Celesteà Zy. Your exclusive order is registered.',
        'order_id' => $orderId
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>
