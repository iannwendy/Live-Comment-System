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
$commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;

// Validate input
if ($commentId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid comment ID'
    ]);
    exit;
}

try {
    // Get username from session
    $username = $_SESSION['username'];
    
    // First check if the comment exists and belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM comments WHERE id = :comment_id AND username = :username");
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Comment not found or you do not have permission to delete it'
        ]);
        exit;
    }
    
    // Prepare SQL statement to delete the comment
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :comment_id AND username = :username");
    
    // Bind parameters
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    
    // Execute the statement
    $stmt->execute();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Comment deleted successfully'
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>