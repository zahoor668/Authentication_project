<?php

require 'config.php';
require 'csrf.php';
$errors = [];

global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // CSRF validation
    if (!validateCsrfToken($_POST['csrf_token'])) 
    {
        echo json_encode(array("success" => false, "errors" => "csrf", "message" => "Invalid CSRF Token"));
        exit();
    }

    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $rememberMe = isset($_POST['rememberMe']) ? $_POST['rememberMe'] : 0;

    // Check if username and password are provided
    if (empty($username) || empty($password)) 
    {
        echo json_encode(array("success" => false, "errors" => "common_error", "message" => "All fields are required"));
        exit();
    }

    // Fetch the user based on username or email
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = :username OR email = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password'])) 
    {
        // Set session variables for the logged-in user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        if ($rememberMe) 
        {

            $token = bin2hex(random_bytes(32)); // Generate a random token
            $expiryTime = time() + (86400 * 30); // Set cookie to expire in 30 days

            // Store the token in a cookie
            setcookie('remember_me', $token, $expiryTime, "/", "", false, true); // HttpOnly for security

            // Store the token in the database
            $stmt = $conn->prepare('UPDATE users SET remember_token = :token WHERE id = :id');
            $stmt->execute(['token' => $token, 'id' => $user['id']]);
        }

        // Success response
        echo json_encode(array('success' => true, 'message' => 'Login successful!'));
        exit();
    } 
    else 
    {
        // Invalid credentials response
        echo json_encode(array('success' => false, 'errors' => "invalid", 'message' => 'Invalid login credentials.'));
        exit();
    }
}

?>
