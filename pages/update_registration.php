<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $registration_id = $_POST['registration_id'];
    $new_status = $_POST['registration_status'];

    $sqlUpdate = "UPDATE registration SET registration_status = '$new_status' WHERE registration_id = $registration_id";
    
    if ($conn->query($sqlUpdate) === TRUE) {
        $_SESSION['message'] = "<div class='alert success'>Registration status updated successfully.</div>";
        header("Location: http://localhost:3000/admin_dashboard.php?section=admin_registrations");
        exit();
    } else {
        echo "<script>alert('Failed to update registration status: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
