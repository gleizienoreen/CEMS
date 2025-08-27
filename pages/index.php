<?php
session_start();
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System - Join</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .join-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #212c61;
            border-radius: 10px;
            padding: 40px 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .logo {
            max-width: 80px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 28px;
            color: #ffd000;
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .label {
            font-size: 18px;
            font-style: italic;
            color: #f7f7f7;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .btn-choice, .btn-create-account {
            width: 100%;
            padding: 12px 0;
            font-size: 18px;
            font-weight: bold;
            background-color: #f7f7f7;
            color: #212c61;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
            text-align: center;
            display: block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-choice:hover, .btn-create-account:hover {
            background-color: #ffd000;
            color: #212c61;
            transform: scale(1.05);
        }

        .btn-create-account {
            background-color: #ffd000;
            color: #212c61;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="join-container">
        <img src="images/logo.png" alt="AU Logo" class="logo">
        <h1 class="title">Campus Event Management System</h1>
        <p class="label">You're here as?</p>

        <a href="login-form.php" class="btn-choice">Administrator</a>
        <a href="login-form.php" class="btn-choice">Student</a>
        <a href="student_signup.php" class="btn-create-account">Create Account</a>
    </div>
</body>
</html>
