<?php
// Include database configuration
require_once 'config.php';

// Set header to JSON
header('Content-Type: application/json');

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get the last comment ID from the request (if any)
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

try {
    // Prepare SQL statement to get comments newer than the last ID
    // Order by ID descending to get newest first
    if ($lastId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE id > :last_id ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':last_id', $lastId, PDO::PARAM_INT);
    } else {
        // If no last ID provided, get the most recent comments
        $stmt = $pdo->prepare("SELECT * FROM comments ORDER BY id DESC LIMIT 20");
    }
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch all comments
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return success response with comments
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>