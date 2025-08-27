<?php 
session_start();
include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email']; 
    $section = $_POST['section']; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = 'Passwords do not match!'; 
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'section' => $section, 
            'password' => '', 
            'confirm_password' => ''
        ];
        header("Location: user.php"); 
        exit();
    }

    $sql = "UPDATE students SET first_name = '$first_name', last_name = '$last_name', section = '$section'"; 

    
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = '$hashed_password'";
    }

    $sql .= " WHERE email = '$email'"; 

    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "<div class='alert success'>Profile information updated successfully.</div>";
    } else {
        $_SESSION['error_message'] = 'Error updating profile: ' . $conn->error; 
    }

    $conn->close();
    header("Location: user.php"); 
    exit();
}
?>
