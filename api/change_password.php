<?php
// Include database configuration
require_once 'config.php';

// Set header to JSON
header('Content-Type: application/json');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get and validate input data
$currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';

// Validate input
if (empty($currentPassword) || empty($newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'Current password and new password are required'
    ]);
    exit;
}

// Validate password length
if (strlen($newPassword) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'New password must be at least 6 characters long'
    ]);
    exit;
}

try {
    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Get current user data
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify current password
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Current password is incorrect'
        ]);
        exit;
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
    
    // Bind parameters
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>