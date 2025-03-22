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
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validate input
if (empty($content)) {
    echo json_encode([
        'success' => false,
        'message' => 'Comment content is required'
    ]);
    exit;
}

// Prevent too long inputs
if (strlen($content) > 1000) {
    echo json_encode([
        'success' => false,
        'message' => 'Comment is too long (maximum 1000 characters)'
    ]);
    exit;
}

try {
    // Get username from session
    $username = $_SESSION['username'];
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO comments (username, content) VALUES (:username, :content)");
    
    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    
    // Execute the statement
    $stmt->execute();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Comment added successfully',
        'comment_id' => $pdo->lastInsertId()
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>