<?php
// Include database configuration
require_once 'config.php';

// Set header to JSON
header('Content-Type: application/json');

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
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validate input
if (empty($username) || empty($content)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username and content are required'
    ]);
    exit;
}

// Prevent too long inputs
if (strlen($username) > 100) {
    echo json_encode([
        'success' => false,
        'message' => 'Username is too long (maximum 100 characters)'
    ]);
    exit;
}

if (strlen($content) > 1000) {
    echo json_encode([
        'success' => false,
        'message' => 'Comment is too long (maximum 1000 characters)'
    ]);
    exit;
}

try {
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