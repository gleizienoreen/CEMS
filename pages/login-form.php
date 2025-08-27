<?php
session_start(); 
include 'connect.php'; 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logIn'])) {
  
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $student_query = "SELECT * FROM students WHERE email = '$email'";
    $student_result = $conn->query($student_query);

    if ($student_result && $student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc(); 

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['studentCourse'] = $row['course']; 
            $_SESSION['user_role'] = 'student';

            header("Location: user.php"); 
            exit();
        }else{
            $error = "Invalid email or password";
        }
    }

    $admin_query = "SELECT * FROM admins WHERE email = '$email'";
    $admin_result = $conn->query($admin_query);

    if ($admin_result && $admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();
    
        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['user_role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        } 
        elseif ($password === $admin['password']) {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE admins SET password = '$hashed_password' WHERE email = '$email'";
            $conn->query($update_query);
    
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['user_role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        }else{
            $error = "Invalid email or password";
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>

    <style>
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
 
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-image: linear-gradient(
                rgba(42, 49, 113, 0.6),
                rgba(27, 27, 27, 0.6)
            ), url('images/bg-home.png');
        }   
        .login-container{
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #212c61;
            border-radius: 10px;
            padding: 48px 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .logo{
            max-width: 100px;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }
        .title{
            font-size: 28px;
            color: #ffd000;
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .input-field{
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: #212c61;
            transition: all 0.3s ease;
        }
        .input-field:focus{
            border-color: #ffd000;
            box-shadow: 0 4px 8px rgba(255, 208, 0, 0.3);
        }
        .btn-login{
            width: 100%;
            padding: 12px 0;
            font-size: 18px;
            font-weight: bold;
            background-color: #ffd000;
            color: #212c61;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3 ease, transform 0.2s ease;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: center;
            display: block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-login:hover{
            background-color: #f7f7f7;
            color: #212c61;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .create-account{
            font-size: 14px;
            color: #f7f7f7;
            margin-bottom: 20px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .create-account:hover{
            color: #ffd000;
            text-decoration: underline;
        }
        .error {
        color: #d8000c; 
        background-color: rgba(255, 223, 223, 0.8);
        border: 1px solid rgba(255, 0, 0, 0.3); 
        padding: 5px; 
        border-radius: 5px; 
        font-size: 0.95em;
        margin-top: 2px; 
        text-align: left;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

</style>
</head>
<body>
<body>
<div class="login-container">
    <img src="images/logo.png" alt="Logo" class="logo">
    <h1 class="title">Campus Event Management System</h1>
    
    <form action="login-form.php" method="POST">
        <input type="email" name="email" placeholder="Email" class="input-field" required>
        <input type="password" name="password" placeholder="Password" class="input-field" required>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <button type="submit" name="logIn" class="btn-login">Log In</button>
        
        <div style="text-align: center;">
            <a href="student_signup.php" class="create-account">Don't have an account yet? Join Now</a>
        </div>
    </form>
</div>
</body>
</body>
</html>

