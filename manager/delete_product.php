<?php
// Start session with same secure settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'use_strict_mode' => true
]);

require __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

// Validate CSRF token
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
    die("CSRF token missing");
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    // Log detailed error for debugging
    error_log("CSRF token mismatch. Session: " . $_SESSION['csrf_token'] . 
             " Received: " . $_POST['csrf_token']);
    die("CSRF token validation failed");
}

// Optional: Validate token age (expire after 1 hour)
if (isset($_SESSION['csrf_token_time']) && 
    (time() - $_SESSION['csrf_token_time'] > 3600)) {
    die("CSRF token expired");
}

// Process deletion
if (isset($_POST['id'])) {
    $product_id = (int)$_POST['id'];
    
    try {
        $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        // Regenerate token after successful operation
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
        
        header("Location: view_products.php?delete_success=1");
        exit();
    } catch(PDOException $e) {
        error_log("Delete failed: " . $e->getMessage());
        header("Location: view_products.php?delete_error=1");
        exit();
    }
}

header("Location: view_products.php");
exit();
?>