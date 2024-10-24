<?php

require 'config.php';
require 'csrf.php';


    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        //  checking csrf
        if (!validateCsrfToken($_POST['csrf_token'])) 
        {
            echo json_encode(array("success"=>false,"errors"=>"csrf","message"=>"Invalid Csrf Token"));
            exit();
        }
    
        // input sanitization
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = $_POST['password'];
    
        // input validation
        if (empty($username) || empty($email) || empty($password)) 
        {
            echo json_encode(array("success"=>false,"errors"=>"common_error","message"=>"All the Field are reuired"));
            exit();
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            echo json_encode(array("success"=>false,"errors"=>"email_error","message"=>"Invalid Email Format"));
            exit();
        }
    
        if (strlen($password) < 6) 
        {
            echo json_encode(array("success"=>false,"errors"=>"password_error","message"=>"Password must be at least 6 characters."));
            exit();
        }
    
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(["username" => $username, "email" => $email]);
        if ($stmt->rowCount() > 0) 
        {
            echo json_encode(array("success"=>false,"errors"=>"account_exist","message"=>"Username or Email is already taken."));
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        
            // Execute the query and check the result
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $passwordHash
            ]);
            if ($result) 
            {
                echo json_encode(array('success' => true, 'message' => 'User registered successfully!'));
                exit();
            } else {
                echo json_encode(array('success' => false, 'message' => 'Failed to register user.'));
                exit();
            }
        
        } 
        catch (PDOException $e) 
        {
            // Catch and handle any errors
            echo json_encode(array('success' => false, 'message' => 'Error: ' . $e->getMessage()));
            exit();
        }
        
    }

    
?>