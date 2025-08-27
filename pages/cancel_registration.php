<?php
session_start();

require 'connect.php'; 

if (!isset($_SESSION['student_id'])) {
    header("Location: login-form.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];

    $sql = "UPDATE registration SET registration_status = 'Cancelled' WHERE registration_id = '$registration_id' AND student_id = '{$_SESSION['student_id']}'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "<div class='alert success'>Your registration request has been cancelled successfully.</div>";
    } else {
        $_SESSION['message'] = "<div class='alert error'>Error canceling registration: " . mysqli_error($conn) . "</div>";
    }

    header("Location: http://localhost:3000/user.php?section=registrations"); 
    exit();
}

mysqli_close($conn);
?>
