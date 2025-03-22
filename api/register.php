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
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
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

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 6 characters long'
    ]);
    exit;
}

try {
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        exit;
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    
    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    
    // Execute the statement
    $stmt->execute();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user_id' => $pdo->lastInsertId()
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>