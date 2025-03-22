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
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username/email and password are required'
    ]);
    exit;
}

try {
    // Check if the username is an email or username
    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
    
    // Prepare SQL statement based on input type
    if ($isEmail) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :username LIMIT 1");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    }
    
    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Store user data in session (excluding password)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    } else {
        // Return error for invalid credentials
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username/email or password'
        ]);
    }
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>