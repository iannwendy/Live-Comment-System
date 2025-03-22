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
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

// Validate input
if (empty($username)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username is required'
    ]);
    exit;
}

// Validate username length
if (strlen($username) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Username must be at least 3 characters long'
    ]);
    exit;
}

try {
    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Check if username already exists for another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :user_id");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("UPDATE users SET username = :username WHERE id = :user_id");
    
    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Update session data
    $_SESSION['username'] = $username;
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>