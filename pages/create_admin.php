<?php
session_start();
include 'connect.php'; 


$firstName = $lastName = $email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['admin_first_name']);
    $lastName = trim($_POST['admin_last_name']);
    $email = trim($_POST['admin_email']);
    $password = trim($_POST['admin_password']);
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $_SESSION['message'] = "<div class='alert error'>All fields are required.</div>"; 
    }

    if (empty($errors)) {
        $sqlCheckEmail = "SELECT admin_id FROM admins WHERE email = '$email'";
        $result = $conn->query($sqlCheckEmail);

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "<div class='alert error'>Email already exists. Please use a different email.</div>"; 
        }
    }

    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sqlInsertAdmin = "INSERT INTO admins (first_name, last_name, email, password) VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
        
        if ($conn->query($sqlInsertAdmin) === TRUE) {
            $_SESSION['message'] = "<div class='alert success'>Successfully added an admin account.</div>"; 
            header("Location: http://localhost:3000/admin_dashboard.php?section=manage_accounts");
            exit;
        } else {
            $_SESSION['message']  = "<div class='alert error'>Error creating account:". $conn->error. "</div>";
        }
    }
 
    $conn->close();
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
}
?>
