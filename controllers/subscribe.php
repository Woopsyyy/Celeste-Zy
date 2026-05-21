<?php
/**
 * Celesteà Zy - Newsletter Subscription Endpoint (Async AJAX Controller)
 * Handles unique email registrations and logs status.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Email address is required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address format.']);
    exit;
}

// Include db.php from relative config folder
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDBConnection(true);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `newsletter` WHERE `email` = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'You are already a valued member of the Inner Circle.'
        ]);
        exit;
    }
    
    // Insert new subscription
    $insertStmt = $pdo->prepare("INSERT INTO `newsletter` (`email`) VALUES (?)");
    $insertStmt->execute([$email]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Welcome to Celesteà Zy. Your exclusive invitation has been sent.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred during verification: ' . $e->getMessage()
    ]);
}
?>
