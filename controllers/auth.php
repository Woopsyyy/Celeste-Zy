<?php
/**
 * Celesteà Zy - Authentication AJAX Controller
 * Handles user login, registration, and logout operations securely using PDO and session management.
 */

// Establish session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../config/db.php';

// Support both form-data and JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $_POST['action'] ?? $input['action'] ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing authentication action.']);
    exit;
}

try {
    $pdo = getDBConnection(true);

    switch ($action) {
        case 'login':
            $email = trim($_POST['email'] ?? $input['email'] ?? '');
            $password = $_POST['password'] ?? $input['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please provide both email and password.']);
                exit;
            }

            // Fetch user record
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session metrics
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                $redirect = ($user['role'] === 'admin') ? 'admin.php' : 'index.php';
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Welcome back, ' . htmlspecialchars($user['name']) . '.',
                    'user' => [
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ],
                    'redirect' => $redirect
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email credentials or incorrect password.']);
            }
            break;

        case 'register':
            $name = trim($_POST['name'] ?? $input['name'] ?? '');
            $email = trim($_POST['email'] ?? $input['email'] ?? '');
            $password = $_POST['password'] ?? $input['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? $input['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                echo json_encode(['success' => false, 'message' => 'All registration fields are required.']);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }

            if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
                exit;
            }

            if ($password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
                exit;
            }

            // Check if email already registered
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'This email address is already registered.']);
                exit;
            }

            // Hash password and insert user
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (?, ?, ?, 'customer')");
            $insertStmt->execute([$name, $email, $hashedPassword]);

            // Set session for automatically logging in the newly registered user
            $newUserId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'customer';

            echo json_encode([
                'success' => true,
                'message' => 'Account successfully created. Welcome to Celesteà Zy!',
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'role' => 'customer'
                ],
                'redirect' => 'index.php'
            ]);
            break;

        case 'logout':
            // Destructuring active sessions
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            echo json_encode([
                'success' => true,
                'message' => 'You have logged out safely. Until next time.'
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Requested authentication action is unrecognized.']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication processing failed: ' . $e->getMessage()
    ]);
}
?>
